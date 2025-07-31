<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Car Wash',
                'slug' => 'car-wash',
                'description' => 'Complete car washing solutions',
                'icon' => 'fas fa-spray-can',
                'parent_id' => null,
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Soap & Shampoo', 'slug' => 'soap-shampoo', 'sort_order' => 1],
                    ['name' => 'Wax & Polish', 'slug' => 'wax-polish', 'sort_order' => 2],
                    ['name' => 'Microfiber Cloths', 'slug' => 'microfiber-cloths', 'sort_order' => 3],
                ]
            ],
            [
                'name' => 'Interior Care',
                'slug' => 'interior-care',
                'description' => 'Interior cleaning and protection products',
                'icon' => 'fas fa-car-side',
                'parent_id' => null,
                'sort_order' => 2,
                'children' => [
                    ['name' => 'Dashboard Cleaners', 'slug' => 'dashboard-cleaners', 'sort_order' => 1],
                    ['name' => 'Seat Cleaners', 'slug' => 'seat-cleaners', 'sort_order' => 2],
                    ['name' => 'Air Fresheners', 'slug' => 'air-fresheners', 'sort_order' => 3],
                ]
            ],
            [
                'name' => 'Tire & Wheel Care',
                'slug' => 'tire-wheel-care',
                'description' => 'Tire and wheel maintenance products',
                'icon' => 'fas fa-circle',
                'parent_id' => null,
                'sort_order' => 3,
                'children' => [
                    ['name' => 'Tire Shine', 'slug' => 'tire-shine', 'sort_order' => 1],
                    ['name' => 'Wheel Cleaners', 'slug' => 'wheel-cleaners', 'sort_order' => 2],
                    ['name' => 'Tire Pressure Tools', 'slug' => 'tire-pressure-tools', 'sort_order' => 3],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $category = Category::create($categoryData);
            
            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                $childData['description'] = $childData['description'] ?? '';
                $childData['icon'] = $childData['icon'] ?? 'fas fa-tag';
                Category::create($childData);
            }
        }
    }
}
