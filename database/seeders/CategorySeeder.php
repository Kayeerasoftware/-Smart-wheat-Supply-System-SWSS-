<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Raw Materials',
                'description' => 'Raw materials used in wheat processing',
                'parent_id' => null,
            ],
            [
                'name' => 'Finished Products',
                'description' => 'Finished wheat products ready for distribution',
                'parent_id' => null,
            ],
            [
                'name' => 'Wheat Grains',
                'description' => 'Various types of wheat grains',
                'parent_id' => 1, // Raw Materials
            ],
            [
                'name' => 'Flour Products',
                'description' => 'Different types of wheat flour',
                'parent_id' => 2, // Finished Products
            ],
            [
                'name' => 'Bakery Products',
                'description' => 'Baked goods made from wheat',
                'parent_id' => 2, // Finished Products
            ],
            [
                'name' => 'Packaging Materials',
                'description' => 'Materials used for packaging wheat products',
                'parent_id' => 1, // Raw Materials
            ],
            [
                'name' => 'Organic Wheat',
                'description' => 'Certified organic wheat products',
                'parent_id' => 3, // Wheat Grains
            ],
            [
                'name' => 'Whole Grain Flour',
                'description' => 'Whole grain wheat flour varieties',
                'parent_id' => 4, // Flour Products
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'parent_id' => $categoryData['parent_id'],
                'is_active' => true,
            ]);
        }
    }
}
