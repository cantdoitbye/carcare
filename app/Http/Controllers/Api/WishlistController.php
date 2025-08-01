<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $productId = $request->product_id;

        // Check if product is active
        $product = Product::where('id', $productId)
                         ->where('status', 'active')
                         ->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found or not available'
            ], 404);
        }

        // Check if already in wishlist
        $existingWishlistItem = Wishlist::where('user_id', $user->id)
                                       ->where('product_id', $productId)
                                       ->first();

        if ($existingWishlistItem) {
            // Remove from wishlist (toggle functionality)
            $existingWishlistItem->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Product removed from wishlist',
                'data' => [
                    'in_wishlist' => false
                ]
            ]);
        }

        // Add to wishlist
        $wishlistItem = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to wishlist successfully',
            'data' => [
                'in_wishlist' => true,
                'wishlist_item' => [
                    'id' => $wishlistItem->id,
                    'product_id' => $productId,
                    'added_at' => $wishlistItem->created_at,
                ]
            ]
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        $wishlistItems = Wishlist::where('user_id', $user->id)
                               ->with(['product.images', 'product.categories'])
                               ->latest()
                               ->get();

        $transformedItems = $wishlistItems->map(function ($item) {
            return [
                'id' => $item->id,
                'added_at' => $item->created_at,
                'product' => $this->transformProduct($item->product),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'wishlist_items' => $transformedItems,
                'total_items' => $wishlistItems->count(),
            ]
        ]);
    }

    private function transformProduct($product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'brand' => $product->brand,
            'price' => (float) $product->price,
            'compare_price' => $product->compare_price ? (float) $product->compare_price : null,
            'discount_percentage' => (float) $product->discount_percentage,
            'is_featured' => $product->is_featured,
            'stock_status' => $product->stock_status,
            'quantity' => $product->quantity,
            'categories' => $product->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            }),
            'primary_image' => $product->images->where('is_primary', true)->first() 
                ? asset('storage/' . $product->images->where('is_primary', true)->first()->image_path)
                : ($product->images->first() ? asset('storage/' . $product->images->first()->image_path) : null),
        ];
    }
}