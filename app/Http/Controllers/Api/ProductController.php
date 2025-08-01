<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $featured = $request->boolean('featured', false);
        
        $query = Product::active()->with(['categories', 'images']);
        
        if ($featured) {
            $query->featured();
        }
        
        $products = $query->paginate($perPage);
        
        $products->getCollection()->transform(function ($product) {
            return $this->transformProduct($product);
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'has_more_pages' => $products->hasMorePages(),
                ]
            ]
        ]);
    }

    public function filter(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $categoryId = $request->get('category_id');
        $subcategoryId = $request->get('subcategory_id');
        $brand = $request->get('brand');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $query = Product::active()->with(['categories', 'images']);
        
        // Filter by category
        if ($categoryId) {
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }
        
        // Filter by subcategory  
        if ($subcategoryId) {
            $query->whereHas('categories', function ($q) use ($subcategoryId) {
                $q->where('categories.id', $subcategoryId);
            });
        }
        
        // Filter by brand
        if ($brand) {
            $query->where('brand', $brand);
        }
        
        // Filter by price range
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        
        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%");
            });
        }
        
        // Sort products
        $allowedSorts = ['name', 'price', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }
        
        $products = $query->paginate($perPage);
        
        $products->getCollection()->transform(function ($product) {
            return $this->transformProduct($product);
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'products' => $products->items(),
                'filters_applied' => [
                    'category_id' => $categoryId,
                    'subcategory_id' => $subcategoryId,
                    'brand' => $brand,
                    'price_range' => [
                        'min' => $minPrice,
                        'max' => $maxPrice
                    ],
                    'search' => $search,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                ],
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'has_more_pages' => $products->hasMorePages(),
                ]
            ]
        ]);
    }

    public function related(Request $request, $slug)
    {
        $product = Product::active()->where('slug', $slug)->with(['categories'])->firstOrFail();
        $limit = $request->get('limit', 8);
        
        // Get related products based on categories and brand
        $categoryIds = $product->categories->pluck('id');
        
        $relatedProducts = Product::active()
                                ->with(['categories', 'images'])
                                ->where('id', '!=', $product->id)
                                ->where(function ($query) use ($categoryIds, $product) {
                                    $query->whereHas('categories', function ($q) use ($categoryIds) {
                                        $q->whereIn('categories.id', $categoryIds);
                                    })
                                    ->orWhere('brand', $product->brand);
                                })
                                ->inRandomOrder()
                                ->limit($limit)
                                ->get()
                                ->map(function ($product) {
                                    return $this->transformProduct($product);
                                });

        return response()->json([
            'status' => 'success',
            'data' => [
                'related_products' => $relatedProducts
            ]
        ]);
    }

    private function transformProduct($product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'short_description' => $product->short_description,
            'sku' => $product->sku,
            'brand' => $product->brand,
            'price' => (float) $product->price,
            'compare_price' => $product->compare_price ? (float) $product->compare_price : null,
            'discount_percentage' => (float) $product->discount_percentage,
            'is_featured' => $product->is_featured,
            'is_digital' => $product->is_digital,
            'stock_status' => $product->stock_status,
            'quantity' => $product->quantity,
            'weight' => $product->weight ? (float) $product->weight : null,
            'categories' => $product->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            }),
            'images' => $product->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('storage/' . $image->image_path),
                    'alt_text' => $image->alt_text,
                    'is_primary' => $image->is_primary,
                ];
            }),
            'primary_image' => $product->images->where('is_primary', true)->first() 
                ? asset('storage/' . $product->images->where('is_primary', true)->first()->image_path)
                : ($product->images->first() ? asset('storage/' . $product->images->first()->image_path) : null),
            'tags' => $product->tags,
            'meta_title' => $product->meta_title,
            'meta_description' => $product->meta_description,
        ];
    }
}