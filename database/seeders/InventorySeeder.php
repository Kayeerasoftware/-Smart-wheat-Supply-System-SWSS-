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
            $this->command->warn('Test supplier not found. Please run TestSupplierSeeder first.');
            return;
        }

        // Get products associated with this supplier
        $products = Product::where('supplier_id', $supplier->id)->get();
        
        if ($products->isEmpty()) {
            $this->command->warn('No products found for supplier. Please run ProductSeeder first.');
            return;
        }

        // Get warehouses
        $warehouses = Warehouse::all();
        
        if ($warehouses->isEmpty()) {
            $this->command->warn('No warehouses found. Please run WarehouseSeeder first.');
            return;
        }

        // Create inventory records
        foreach ($products as $product) {
            foreach ($warehouses->take(2) as $warehouse) {
                $quantityOnHand = rand(50, 500);
                $quantityReserved = rand(0, 50);
                $quantityAvailable = $quantityOnHand - $quantityReserved;
                
                Inventory::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
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
                        'expiry_date' => now()->addMonths(rand(6, 24)),
                        'status' => 'active',
                        'attributes' => json_encode([
                            'supplier_id' => $supplier->id,
                            'quality_grade' => 'A',
                            'storage_conditions' => 'Dry, Cool'
                        ]),
                    ]
                );
            }
        }

        $this->command->info('Inventory data created successfully for test supplier.');
    }
} 