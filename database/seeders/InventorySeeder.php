<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the test supplier
        $supplier = User::where('email', 'supplier@gmail.com')->first();
        
        if (!$supplier) {
            $this->command->warn('Supplier not found. Please run SupplierUserSeeder first.');
            return;
        }

        // Get products associated with this supplier
        $products = Product::where('type', 'raw')->where('name', 'Wheat')->get();
        
        if ($products->isEmpty()) {
            $this->command->warn('No raw wheat product found. Please run ProductSeeder first.');
            return;
        }

        // Get warehouses
        $warehouses = Warehouse::all();
        
        if ($warehouses->isEmpty()) {
            $this->command->warn('No warehouses found. Please run WarehouseSeeder first.');
            return;
        }

        $startDate = now()->subYears(5)->startOfMonth();
        $endDate = now();
        $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);
        // Create inventory records
        foreach ($products as $product) {
            foreach ($warehouses->take(2) as $warehouse) {
                foreach ($period as $date) {
                    $quantityOnHand = rand(50, 500);
                    $quantityReserved = rand(0, 50);
                    $quantityAvailable = $quantityOnHand - $quantityReserved;
                    
                    Inventory::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouse->id,
                            'created_at' => $date,
                        ],
                        [
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouse->id,
                            'quantity_on_hand' => $quantityOnHand,
                            'quantity_available' => $quantityAvailable,
                            'quantity_reserved' => $quantityReserved,
                            'quantity_on_order' => rand(0, 100),
                            'average_cost' => $product->cost_price ?? 1.50,
                            'location' => 'A' . rand(1, 10) . '-' . rand(1, 20),
                            'batch_number' => 'BATCH-' . strtoupper(substr(md5(rand()), 0, 8)),
                            'expiry_date' => $date->copy()->addMonths(rand(6, 24)),
                            'status' => 'active',
                            'attributes' => json_encode([
                                'supplier_id' => $supplier->id,
                                'quality_grade' => 'A',
                                'storage_conditions' => 'Dry, Cool'
                            ]),
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]
                    );
                }
            }
        }

        $this->command->info('5 years of monthly inventory data created for the main supplier.');
    }
} 