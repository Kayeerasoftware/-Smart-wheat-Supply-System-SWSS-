<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a supplier user to associate products with
        $supplier = User::where('role', 'supplier')->first();
        
        if (!$supplier) {
            // Create a supplier user if none exists
            $supplier = User::create([
                'username' => 'supplier1',
                'email' => 'supplier1@example.com',
                'password' => bcrypt('password'),
                'role' => 'supplier',
                'phone' => '+1234567893',
                'address' => '123 Supplier St, City, State 12345',
                'status' => 'active'
            ]);
        }

        $products = [
            [
                'name' => 'Premium Wheat Flour',
                'category_name' => 'Flour Products',
                'brand' => 'Golden Harvest',
                'description' => 'High-quality all-purpose wheat flour for baking',
                'unit_of_measure' => 'kg',
                'unit_price' => 2.50,
                'cost_price' => 1.80,
                'reorder_point' => 100,
                'reorder_quantity' => 500,
                'is_raw_material' => false,
                'is_finished_good' => true,
            ],
            [
                'name' => 'Organic Whole Wheat',
                'category_name' => 'Organic Wheat',
                'brand' => 'Nature\'s Best',
                'description' => 'Certified organic whole wheat grains',
                'unit_of_measure' => 'kg',
                'unit_price' => 3.20,
                'cost_price' => 2.40,
                'reorder_point' => 50,
                'reorder_quantity' => 200,
                'is_raw_material' => true,
                'is_finished_good' => false,
            ],
            [
                'name' => 'Bread Flour',
                'category_name' => 'Flour Products',
                'brand' => 'Baker\'s Choice',
                'description' => 'Specialized flour for bread making',
                'unit_of_measure' => 'kg',
                'unit_price' => 3.00,
                'cost_price' => 2.10,
                'reorder_point' => 75,
                'reorder_quantity' => 300,
                'is_raw_material' => false,
                'is_finished_good' => true,
            ],
            [
                'name' => 'Wheat Packaging Bags',
                'category_name' => 'Packaging Materials',
                'brand' => 'PackPro',
                'description' => '25kg bags for wheat packaging',
                'unit_of_measure' => 'pieces',
                'unit_price' => 0.50,
                'cost_price' => 0.30,
                'reorder_point' => 200,
                'reorder_quantity' => 1000,
                'is_raw_material' => true,
                'is_finished_good' => false,
            ],
            [
                'name' => 'Fresh Baked Bread',
                'category_name' => 'Bakery Products',
                'brand' => 'Daily Fresh',
                'description' => 'Freshly baked whole wheat bread',
                'unit_of_measure' => 'pieces',
                'unit_price' => 4.50,
                'cost_price' => 2.80,
                'reorder_point' => 20,
                'reorder_quantity' => 50,
                'is_raw_material' => false,
                'is_finished_good' => true,
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category_name'])->first();
            
            if ($category) {
                Product::updateOrCreate(
                    ['name' => $productData['name']],
                    [
                    'name' => $productData['name'],
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'category_id' => $category->id,
                        'supplier_id' => $supplier->id,
                    'brand' => $productData['brand'],
                    'description' => $productData['description'],
                    'unit_of_measure' => $productData['unit_of_measure'],
                    'unit_price' => $productData['unit_price'],
                    'cost_price' => $productData['cost_price'],
                    'reorder_point' => $productData['reorder_point'],
                    'reorder_quantity' => $productData['reorder_quantity'],
                    'is_raw_material' => $productData['is_raw_material'],
                    'is_finished_good' => $productData['is_finished_good'],
                    'status' => 'active',
                    ]
                );
            }
        }
    }
}
