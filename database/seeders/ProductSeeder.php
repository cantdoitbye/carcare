<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $products = [
            [
                'name' => 'Premium Car Wash Kit',
                'slug' => 'premium-car-wash-kit',
                'description' => 'Complete car washing kit with premium soap, microfiber cloths, and accessories.',
                'short_description' => 'Everything you need for a professional car wash.',
                'sku' => 'CWK-001',
                'brand' => 'CarCare Pro',
                'price' => 89.99,
                'compare_price' => 119.99,
                'cost_price' => 45.00,
                'discount_percentage' => 25.00,
                'quantity' => 50,
                'min_quantity' => 5,
                'weight' => 2.5,
                'status' => 'active',
                'is_featured' => true,
                'categories' => ['car-wash'],
            ],
            [
                'name' => 'Tire Shine Spray',
                'slug' => 'tire-shine-spray',
                'description' => 'Long-lasting tire shine spray for a glossy finish.',
                'short_description' => 'Professional grade tire shine.',
                'sku' => 'TSS-002',
                'brand' => 'ShineMaster',
                'price' => 24.99,
                'compare_price' => 29.99,
                'cost_price' => 12.00,
                'quantity' => 100,
                'min_quantity' => 10,
                'weight' => 0.5,
                'status' => 'active',
                'is_featured' => false,
                'categories' => ['tire-wheel-care'],
            ],
            [
                'name' => 'Interior Cleaner All-Purpose',
                'slug' => 'interior-cleaner-all-purpose',
                'description' => 'Versatile interior cleaner safe for all surfaces.',
                'short_description' => 'Clean dashboard, seats, and more.',
                'sku' => 'IC-003',
                'brand' => 'CleanMax',
                'price' => 19.99,
                'cost_price' => 8.00,
                'quantity' => 75,
                'min_quantity' => 8,
                'weight' => 0.75,
                'status' => 'active',
                'is_featured' => true,
                'categories' => ['interior-care'],
            ],
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData['categories'][0];
            unset($productData['categories']);
            
            $product = Product::create($productData);
            
            // Attach category
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $product->categories()->attach($category->id);
            }
        }
    }
}
