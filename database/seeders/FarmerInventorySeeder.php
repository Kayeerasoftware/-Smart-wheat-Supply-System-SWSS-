<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FarmerInventory;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Arr;

class FarmerInventorySeeder extends Seeder
{
    public function run(): void
    {
        $farmers = User::where('role', 'farmer')->get();
        $wheatProducts = Product::where('name', 'like', '%wheat%')->get();
        $grades = ['A', 'B', 'C'];

        if ($farmers->isEmpty() || $wheatProducts->isEmpty()) {
            $this->command->warn('No farmers or wheat products found. Please seed farmers and products first.');
            return;
        }

        foreach ($farmers as $farmer) {
            // Each farmer gets 1-3 wheat products
            $numProducts = min(3, $wheatProducts->count());
            $numToSelect = $numProducts > 1 ? rand(1, $numProducts) : 1;
            $productsForFarmer = $wheatProducts->random($numToSelect);
            // Ensure $productsForFarmer is always a collection
            if (!is_array($productsForFarmer) && !$productsForFarmer instanceof \Illuminate\Support\Collection) {
                $productsForFarmer = collect([$productsForFarmer]);
            }
            foreach ($productsForFarmer as $product) {
                FarmerInventory::create([
                    'farmer_id' => $farmer->id,
                    'product_id' => $product->id,
                    'quantity' => rand(100, 2000),
                    'quality_grade' => Arr::random($grades),
                    'harvest_date' => now()->subDays(rand(0, 60)),
                    'moisture_content' => rand(10, 15) + rand(0, 99)/100,
                    'protein_content' => rand(8, 14) + rand(0, 99)/100,
                    'price_per_kg' => rand(800, 2000) / 100,
                    'location' => 'Farm ' . $farmer->username,
                    'notes' => 'Sample wheat batch',
                    'is_available' => true,
                ]);
            }
        }
        $this->command->info('Dummy wheat inventory created for farmers.');
    }
} 