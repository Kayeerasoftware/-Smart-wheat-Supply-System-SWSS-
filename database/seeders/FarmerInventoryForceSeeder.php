<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\FarmerInventory;
use Illuminate\Support\Str;

class FarmerInventoryForceSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure a farmer exists
        $farmer = User::firstOrCreate(
            ['email' => 'debug_farmer@farm.com'],
            [
                'username' => 'debug_farmer',
                'email' => 'debug_farmer@farm.com',
                'password' => bcrypt('password'),
                'role' => 'farmer',
                'phone' => '+10000000000',
                'address' => 'Debug Farm Lane',
                'status' => 'active',
            ]
        );

        // Ensure a wheat product exists
        $product = Product::firstOrCreate(
            ['name' => 'Debug Wheat'],
            [
                'sku' => 'SKU-' . strtoupper(Str::random(8)),
                'category_id' => 1, // You may need to adjust this
                'supplier_id' => 1, // You may need to adjust this
                'brand' => 'DebugBrand',
                'description' => 'Debug wheat product',
                'unit_of_measure' => 'kg',
                'unit_price' => 2.00,
                'cost_price' => 1.50,
                'reorder_point' => 10,
                'reorder_quantity' => 50,
                'is_raw_material' => true,
                'is_finished_good' => false,
                'status' => 'active',
                'type' => 'raw',
            ]
        );

        // Ensure a FarmerInventory record exists
        FarmerInventory::create([
            'farmer_id' => $farmer->id,
            'product_id' => $product->id,
            'quantity' => 500,
            'quality_grade' => 'A',
            'harvest_date' => now()->subDays(10),
            'moisture_content' => 12.5,
            'protein_content' => 10.2,
            'price_per_kg' => 1.80,
            'location' => 'Debug Farm Lane',
            'notes' => 'Debug batch',
            'is_available' => true,
        ]);
    }
} 