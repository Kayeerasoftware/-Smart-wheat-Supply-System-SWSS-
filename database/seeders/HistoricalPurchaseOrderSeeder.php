<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HistoricalPurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating historical purchase orders...');
        
        $suppliers = User::where('role', 'supplier')->get();
        $vendors = Vendor::all();
        $products = Product::all();
        
        if ($suppliers->isEmpty() || $vendors->isEmpty() || $products->isEmpty()) {
            $this->command->error('Missing required data for purchase orders');
            return;
        }
        
        $startDate = Carbon::now()->subYears(5);
        $endDate = Carbon::now();
        
        // Generate purchase orders with realistic patterns
        $totalPOs = rand(150, 300);
        $posCreated = 0;
        
        for ($i = 0; $i < $totalPOs; $i++) {
            try {
                // Generate date with trend towards recent dates
                $poDate = $this->getRandomDateWithTrend($startDate, $endDate, 0.6);
                
                $supplier = $suppliers->random();
                $vendor = $vendors->random();
                $productsForPO = $products->take(rand(1, 4));
                
                $subtotal = 0;
                $poItems = [];
                
                foreach ($productsForPO as $product) {
                    $quantity = rand(100, 2000);
                    $unitPrice = $product->cost_price + (rand(-5, 10) / 100);
                    $totalPrice = $quantity * $unitPrice;
                    $subtotal += $totalPrice;
                    
                    $poItems[] = [
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ];
                }
                
                $taxAmount = $subtotal * 0.16; // 16% VAT
                $shippingAmount = rand(1000, 5000);
                $discountAmount = $subtotal * (rand(0, 20) / 100);
                $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;
                
                $status = $this->getRandomPOStatus($poDate);
                
                $purchaseOrder = PurchaseOrder::create([
                    'po_number' => 'PO-' . strtoupper(Str::random(10)),
                    'vendor_id' => $vendor->id,
                    'created_by' => $supplier->id,
                    'status' => $status,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'shipping_amount' => $shippingAmount,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'notes' => 'Historical purchase order from ' . $poDate->format('M Y'),
                    'terms_and_conditions' => 'Standard terms and conditions apply',
                    'order_date' => $poDate,
                    'expected_delivery_date' => $poDate->copy()->addDays(rand(7, 30)),
                    'actual_delivery_date' => $status === 'fully_received' ? $poDate->copy()->addDays(rand(5, 25)) : null,
                    'shipping_address' => $supplier->address,
                    'billing_address' => $supplier->address,
                    'payment_terms' => ['net_30', 'net_60', 'immediate'][array_rand(['net_30', 'net_60', 'immediate'])],
                    'payment_status' => $status === 'fully_received' ? 'paid' : 'pending',
                    'approval_status' => 'approved',
                    'approved_by' => $supplier->id,
                    'approved_at' => $poDate->copy()->addDays(rand(1, 3)),
                    'created_at' => $poDate,
                    'updated_at' => $poDate,
                ]);
                
                // Create purchase order items
                foreach ($poItems as $item) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'received_quantity' => $status === 'fully_received' ? $item['quantity'] : rand(0, $item['quantity']),
                        'created_at' => $poDate,
                        'updated_at' => $poDate,
                    ]);
                }
                
                $posCreated++;
                
                if ($posCreated % 30 === 0) {
                    $this->command->info("Created {$posCreated} purchase orders...");
                }
                
            } catch (\Exception $e) {
                $this->command->warn("Error creating purchase order {$i}: " . $e->getMessage());
                continue;
            }
        }
        
        $this->command->info("Successfully created {$posCreated} historical purchase orders!");
    }
    
    private function getRandomDateWithTrend($startDate, $endDate, $trend = 0.5)
    {
        // Generate dates with a trend towards more recent dates
        $daysDiff = $startDate->diffInDays($endDate);
        $randomDays = (int) ($daysDiff * pow(rand(0, 100) / 100, $trend));
        return $startDate->copy()->addDays($randomDays);
    }
    
    private function getRandomPOStatus($poDate)
    {
        $daysSincePO = $poDate->diffInDays(now());
        
        if ($daysSincePO < 30) {
            return ['draft', 'sent', 'confirmed'][array_rand(['draft', 'sent', 'confirmed'])];
        } elseif ($daysSincePO < 60) {
            return ['confirmed', 'partially_received', 'fully_received'][array_rand(['confirmed', 'partially_received', 'fully_received'])];
        } else {
            return 'fully_received'; // Older POs are likely fully received
        }
    }
} 