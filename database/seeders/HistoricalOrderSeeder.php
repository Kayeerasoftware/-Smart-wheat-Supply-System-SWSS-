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

class HistoricalOrderSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating 5 years of historical orders...');
        
        $suppliers = User::where('role', 'supplier')->get();
        $farmers = User::where('role', 'farmer')->get();
        $manufacturers = User::where('role', 'manufacturer')->get();
        $retailers = User::where('role', 'retailer')->get();
        $products = Product::all();
        $warehouses = Warehouse::all();
        
        if ($suppliers->isEmpty() || $farmers->isEmpty() || $manufacturers->isEmpty() || $products->isEmpty()) {
            $this->command->error('Missing required data for orders');
            return;
        }
        
        $startDate = Carbon::now()->subYears(5);
        $endDate = Carbon::now();
        
        // Generate orders with realistic patterns (more orders in recent years)
        $totalOrders = rand(300, 600);
        $ordersCreated = 0;
        
        for ($i = 0; $i < $totalOrders; $i++) {
            try {
                // Generate date with trend towards recent dates
                $orderDate = $this->getRandomDateWithTrend($startDate, $endDate, 0.6);
                
                // Determine order type and participants
                $orderType = rand(1, 10) <= 7 ? 'purchase' : 'sale';
                
                if ($orderType === 'purchase') {
                    // Supplier buying from farmer
                    $customer = $suppliers->random();
                    $vendor = null;
                    $productsForOrder = $products->where('type', 'raw')->take(rand(1, 3));
                } else {
                    // Supplier selling to manufacturer/retailer
                    $customer = collect([$manufacturers, $retailers])->flatten()->random();
                    $vendor = $suppliers->random();
                    $productsForOrder = $products->where('type', 'processed')->take(rand(1, 3));
                }
                
                if ($productsForOrder->isEmpty()) {
                    continue; // Skip if no products available
                }
                
                $subtotal = 0;
                $orderItems = [];
                
                foreach ($productsForOrder as $product) {
                    $quantity = rand(50, 500);
                    $unitPrice = $product->unit_price + (rand(-10, 10) / 100);
                    $totalPrice = $quantity * $unitPrice;
                    $subtotal += $totalPrice;
                    
                    $orderItems[] = [
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ];
                }
                
                $taxAmount = $subtotal * 0.16; // 16% VAT
                $shippingAmount = rand(500, 2000);
                $discountAmount = $subtotal * (rand(0, 15) / 100);
                $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;
                
                $status = $this->getRandomOrderStatus($orderDate);
                
                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                    'customer_id' => $customer->id,
                    'vendor_id' => $vendor ? $vendor->id : null,
                    'order_type' => $orderType,
                    'status' => $status,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'shipping_amount' => $shippingAmount,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'notes' => 'Historical order from ' . $orderDate->format('M Y'),
                    'order_date' => $orderDate,
                    'expected_delivery_date' => $orderDate->copy()->addDays(rand(3, 14)),
                    'actual_delivery_date' => $status === 'delivered' ? $orderDate->copy()->addDays(rand(1, 10)) : null,
                    'shipping_address' => $customer->address,
                    'billing_address' => $customer->address,
                    'payment_method' => ['cash', 'bank_transfer', 'mobile_money'][array_rand(['cash', 'bank_transfer', 'mobile_money'])],
                    'payment_status' => $status === 'delivered' ? 'paid' : 'pending',
                    'tracking_number' => $status !== 'draft' ? 'TRK-' . strtoupper(Str::random(8)) : null,
                    'carrier' => $status !== 'draft' ? ['DHL', 'FedEx', 'UPS', 'Local'][array_rand(['DHL', 'FedEx', 'UPS', 'Local'])] : null,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
                
                // Create order items
                foreach ($orderItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $warehouses->random()->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'discount_amount' => 0,
                        'tax_amount' => $item['total_price'] * 0.16,
                        'status' => $status,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);
                }
                
                $ordersCreated++;
                
                if ($ordersCreated % 50 === 0) {
                    $this->command->info("Created {$ordersCreated} orders...");
                }
                
            } catch (\Exception $e) {
                $this->command->warn("Error creating order {$i}: " . $e->getMessage());
                continue;
            }
        }
        
        $this->command->info("Successfully created {$ordersCreated} historical orders!");
    }
    
    private function getRandomDateWithTrend($startDate, $endDate, $trend = 0.5)
    {
        // Generate dates with a trend towards more recent dates
        $daysDiff = $startDate->diffInDays($endDate);
        $randomDays = (int) ($daysDiff * pow(rand(0, 100) / 100, $trend));
        return $startDate->copy()->addDays($randomDays);
    }
    
    private function getRandomOrderStatus($orderDate)
    {
        $daysSinceOrder = $orderDate->diffInDays(now());
        
        if ($daysSinceOrder < 30) {
            return ['draft', 'pending', 'confirmed', 'processing'][array_rand(['draft', 'pending', 'confirmed', 'processing'])];
        } elseif ($daysSinceOrder < 60) {
            return ['processing', 'shipped', 'delivered'][array_rand(['processing', 'shipped', 'delivered'])];
        } else {
            return 'delivered'; // Older orders are likely delivered
        }
    }
} 