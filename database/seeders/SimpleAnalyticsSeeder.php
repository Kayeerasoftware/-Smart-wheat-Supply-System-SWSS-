<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;

class SimpleAnalyticsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating simple analytics data...');
        
        // Get all products and warehouses
        $products = Product::all();
        $warehouses = Warehouse::all();
        $customers = User::whereIn('role', ['manufacturer', 'retailer', 'distributor'])->get();
        $suppliers = User::where('role', 'supplier')->get();
        
        if ($products->isEmpty() || $warehouses->isEmpty()) {
            $this->command->error('No products or warehouses found');
            return;
        }
        
        // Generate inventory data for the last 12 months
        $this->generateInventoryData($products, $warehouses);
        
        // Generate sales data for the last 12 months
        $this->generateSalesData($products, $customers, $suppliers, $warehouses);
        
        // Generate demand forecast data
        $this->generateDemandForecastData();
        
        $this->command->info('Simple analytics data generated successfully!');
    }
    
    private function generateInventoryData($products, $warehouses)
    {
        $this->command->info('Generating inventory data...');
        
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        $endDate = Carbon::now();
        
        $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);
        
        foreach ($period as $date) {
            foreach ($products as $product) {
                foreach ($warehouses as $warehouse) {
                    // Generate realistic inventory levels
                    $baseQuantity = rand(300, 1500);
                    
                    // Add seasonal variation
                    $month = $date->month;
                    $seasonalMultiplier = 1.0;
                    if (in_array($month, [3, 4, 5, 6])) {
                        $seasonalMultiplier = 1.4;
                    } elseif (in_array($month, [12, 1, 2])) {
                        $seasonalMultiplier = 0.8;
                    }
                    
                    $quantityOnHand = (int) ($baseQuantity * $seasonalMultiplier);
                    $quantityReserved = (int) ($quantityOnHand * 0.15);
                    $quantityAvailable = $quantityOnHand - $quantityReserved;
                    $quantityOnOrder = (int) ($quantityOnHand * 0.25);
                    
                    Inventory::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouse->id,
                            'batch_number' => 'BATCH-' . $date->format('Y-m'),
                        ],
                        [
                            'quantity_on_hand' => $quantityOnHand,
                            'quantity_reserved' => $quantityReserved,
                            'quantity_available' => $quantityAvailable,
                            'quantity_on_order' => $quantityOnOrder,
                            'average_cost' => $product->cost_price + (rand(-5, 5) / 100),
                            'location' => 'A' . rand(1, 10) . '-' . rand(1, 20),
                            'expiry_date' => $date->addMonths(rand(6, 18)),
                            'status' => 'active',
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]
                    );
                }
            }
        }
        
        $this->command->info('Inventory data generated');
    }
    
    private function generateSalesData($products, $customers, $suppliers, $warehouses)
    {
        $this->command->info('Generating sales data...');
        
        $vendors = \App\Models\Vendor::all();
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        $endDate = Carbon::now();
        
        $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);
        
        foreach ($period as $date) {
            // Generate 5-12 orders per month
            $ordersThisMonth = rand(5, 12);
            
            for ($i = 0; $i < $ordersThisMonth; $i++) {
                $customer = $customers->random();
                $supplier = $suppliers->random();
                $productsForOrder = $products->random(rand(1, 3));
                $vendorId = $vendors->isNotEmpty() ? $vendors->random()->id : null;
                
                $subtotal = 0;
                $orderItems = [];
                
                foreach ($productsForOrder as $product) {
                    $quantity = rand(100, 800);
                    $unitPrice = $product->unit_price + (rand(-10, 20) / 100);
                    $totalPrice = $quantity * $unitPrice;
                    $subtotal += $totalPrice;
                    
                    $orderItems[] = [
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ];
                }
                
                $taxAmount = $subtotal * 0.16;
                $shippingAmount = rand(800, 2500);
                $discountAmount = $subtotal * (rand(0, 12) / 100);
                $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;
                
                $order = Order::create([
                    'order_number' => 'ANALYTICS-' . strtoupper(uniqid()),
                    'customer_id' => $customer->id,
                    'vendor_id' => $vendorId,
                    'order_type' => 'purchase',
                    'status' => 'delivered',
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'shipping_amount' => $shippingAmount,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'notes' => 'Analytics sales data - ' . $date->format('M Y'),
                    'order_date' => $date->copy()->addDays(rand(1, 28)),
                    'expected_delivery_date' => $date->copy()->addDays(rand(3, 14)),
                    'actual_delivery_date' => $date->copy()->addDays(rand(1, 10)),
                    'shipping_address' => $customer->address,
                    'billing_address' => $customer->address,
                    'payment_method' => ['cash', 'bank_transfer', 'mobile_money'][array_rand(['cash', 'bank_transfer', 'mobile_money'])],
                    'payment_status' => 'paid',
                    'tracking_number' => 'TRK-' . strtoupper(uniqid()),
                    'carrier' => ['DHL', 'FedEx', 'UPS', 'Local'][array_rand(['DHL', 'FedEx', 'UPS', 'Local'])],
                    'created_at' => $date,
                    'updated_at' => $date,
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
                        'status' => 'delivered',
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }
            }
        }
        
        $this->command->info('Sales data generated');
    }
    
    private function generateDemandForecastData()
    {
        $this->command->info('Generating AI demand forecast data...');
        
        $forecastData = [
            'title' => 'AI-Powered Wheat Demand Forecast',
            'description' => 'Machine learning analysis of historical patterns and market trends',
            'historical_dates' => [],
            'historical_values' => [],
            'forecast_dates' => [],
            'forecast_values' => [],
            'recommendations' => [
                'Increase wheat processing capacity by 18% in Q2 2025',
                'Plan for 920 tons average monthly processing',
                'Total expected demand: 5520 tons over next 6 months',
                'Consider expanding storage facilities in Nairobi region',
                'Monitor weather patterns for harvest season planning',
                'Focus on organic wheat varieties as demand is trending upward'
            ]
        ];
        
        // Generate historical data (last 18 months)
        $startDate = Carbon::now()->subMonths(18);
        for ($i = 0; $i < 18; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $forecastData['historical_dates'][] = $date->format('M Y');
            
            $baseDemand = 650;
            $month = $date->month;
            
            // Seasonal variation
            if (in_array($month, [3, 4, 5, 6])) {
                $demand = $baseDemand * 1.35;
            } elseif (in_array($month, [12, 1, 2])) {
                $demand = $baseDemand * 0.75;
            } else {
                $demand = $baseDemand;
            }
            
            $demand += rand(-60, 120);
            $forecastData['historical_values'][] = (int) $demand;
        }
        
        // Generate forecast data (next 12 months)
        $forecastStart = Carbon::now()->addMonth();
        for ($i = 0; $i < 12; $i++) {
            $date = $forecastStart->copy()->addMonths($i);
            $forecastData['forecast_dates'][] = $date->format('M Y');
            
            $baseForecast = 750 + ($i * 25); // Growing trend
            $month = $date->month;
            
            if (in_array($month, [3, 4, 5, 6])) {
                $forecast = $baseForecast * 1.45;
            } elseif (in_array($month, [12, 1, 2])) {
                $forecast = $baseForecast * 0.8;
            } else {
                $forecast = $baseForecast;
            }
            
            $forecast += rand(-40, 80);
            $forecastData['forecast_values'][] = (int) $forecast;
        }
        
        // Save to JSON file
        $filePath = storage_path('app/demand_forecast_data.json');
        file_put_contents($filePath, json_encode($forecastData, JSON_PRETTY_PRINT));
        
        $this->command->info('AI demand forecast data saved to: ' . $filePath);
    }
} 