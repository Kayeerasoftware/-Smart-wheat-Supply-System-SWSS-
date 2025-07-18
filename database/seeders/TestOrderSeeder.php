<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestOrderSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Testing order generation...');
        
        $suppliers = User::where('role', 'supplier')->get();
        $farmers = User::where('role', 'farmer')->get();
        $products = Product::where('name', 'like', '%wheat%')->get();
        $warehouses = Warehouse::all();
        
        if ($suppliers->isEmpty() || $farmers->isEmpty() || $products->isEmpty() || $warehouses->isEmpty()) {
            $this->command->error('Missing required data for orders');
            return;
        }

        $supplier = $suppliers->first();
        $farmer = $farmers->first();
        $warehouse = $warehouses->first();
        // Find a wheat product that belongs to the supplier
        $product = Product::where('name', 'like', '%wheat%')->where('supplier_id', $supplier->id)->first();
        if (!$product) {
            // If not found, assign the supplier_id to the first wheat product
            $product = Product::where('name', 'like', '%wheat%')->first();
            if ($product) {
                $product->supplier_id = $supplier->id;
                $product->save();
            }
        }
        if (!$product) {
            $this->command->error('No wheat product found to seed orders.');
            return;
        }

        // Seed delivered orders for the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $orderDate = Carbon::now()->subMonths($i)->startOfMonth()->addDays(rand(0, 27));
            $quantity = rand(100, 1000);
            $unitPrice = $product->unit_price ?? 2.0;
            $total = $quantity * $unitPrice;

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_id' => $supplier->id,
                'vendor_id' => $supplier->id, // For simplicity
                'order_type' => 'purchase',
                'status' => 'delivered',
                'subtotal' => $total,
                'tax_amount' => $total * 0.1,
                'shipping_amount' => 50,
                'discount_amount' => 0,
                'total_amount' => $total * 1.1 + 50,
                'notes' => 'Seeded delivered order',
                'order_date' => $orderDate,
                'expected_delivery_date' => $orderDate->copy()->addDays(7),
                'shipping_address' => 'Supplier Address',
                'billing_address' => 'Supplier Billing',
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'actual_delivery_date' => $orderDate->copy()->addDays(7),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $total,
            ]);
        }

        $this->command->info('Seeded delivered orders for supplier sales performance.');
    }
} 