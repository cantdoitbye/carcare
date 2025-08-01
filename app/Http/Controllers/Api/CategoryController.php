<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $parentOnly = $request->boolean('parent_only', false);
        
        $query = Category::active()->ordered();
        
        if ($parentOnly) {
            $query->parent();
        }
        
        $categories = $query->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'image' => $category->image ? asset('storage/' . $category->image) : null,
                'icon' => $category->icon,
                'parent_id' => $category->parent_id,
                'has_children' => $category->hasChildren(),
                'children_count' => $category->children()->count(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => $categories
            ]
        ]);
    }

    public function subcategories(Request $request)
    {
        $categoryId = $request->get('category_id');
        
        $query = Category::active()->ordered();
        
        if ($categoryId) {
            $query->where('parent_id', $categoryId);
        } else {
            $query->whereNotNull('parent_id');
        }
        
        $subcategories = $query->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'image' => $category->image ? asset('storage/' . $category->image) : null,
                'icon' => $category->icon,
                'parent_id' => $category->parent_id,
                'parent_name' => $category->parent->name ?? null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'subcategories' => $subcategories
            ]
        ]);
    }

    public function brands(Request $request)
    {
        $brands = Product::active()
                        ->whereNotNull('brand')
                        ->distinct()
                        ->pluck('brand')
                        ->filter()
                        ->sort()
                        ->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'brands' => $brands
            ]
        ]);
    }

    public function navLinks(Request $request)
    {
        $categories = Category::active()
                            ->parent()
                            ->ordered()
                            ->with(['children' => function ($query) {
                                $query->active()->ordered();
                            }])
                            ->get()
                            ->map(function ($category) {
                                return [
                                    'id' => $category->id,
                                    'name' => $category->name,
                                    'slug' => $category->slug,
                                    'icon' => $category->icon,
                                    'children' => $category->children->map(function ($child) {
                                        return [
                                            'id' => $child->id,
                                            'name' => $child->name,
                                            'slug' => $child->slug,
                                        ];
                                    }),
                                ];
                            });

        $brands = Product::active()
                        ->whereNotNull('brand')
                        ->distinct()
                        ->pluck('brand')
                        ->filter()
                        ->sort()
                        ->take(10) // Limit brands for navigation
                        ->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => $categories,
                'brands' => $brands
            ]
        ]);
    }
}