<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $supplier = User::where('email', 'supplier@gmail.com')->first();
        if (!$supplier) {
            $this->command->warn('Supplier not found. Please run SupplierUserSeeder first.');
            return;
        }
        $farmers = User::where('role', 'farmer')->get();
        $manufacturers = User::where('role', 'manufacturer')->get();
        $rawProducts = Product::where('type', 'raw')->get();
        if ($farmers->isEmpty() || $manufacturers->isEmpty() || $rawProducts->isEmpty()) {
            $this->command->warn('Need at least one farmer, manufacturer, and raw product.');
            return;
        }
        $startDate = now()->subYears(5)->startOfMonth();
        $endDate = now();
        $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);
        foreach ($period as $date) {
            // Purchase from farmer to supplier
            $farmer = $farmers->random();
            $product = $rawProducts->random();
            $quantity = rand(100, 1000);
            $unitPrice = $product->cost_price;
            $total = $quantity * $unitPrice;
            $purchaseOrder = Order::create([
                'order_number' => 'PO-' . strtoupper(Str::random(10)),
                'customer_id' => $supplier->id,
                'vendor_id' => null, // Farmers are not vendors
                'order_type' => 'purchase',
                'status' => 'delivered',
                'total_amount' => $total,
                'discount_amount' => 0,
                'order_date' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            OrderItem::create([
                'order_id' => $purchaseOrder->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $total,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

        }
        $this->command->info('5 years of monthly supplier purchase orders created.');
    }
} 