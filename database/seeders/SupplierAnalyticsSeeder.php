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
use Illuminate\Support\Facades\DB;

class SupplierAnalyticsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating supplier analytics data...');
        
        // Get supplier user
        $supplier = User::where('role', 'supplier')->first();
        if (!$supplier) {
            $this->command->error('No supplier found. Please run user seeders first.');
            return;
        }
        
        // Generate inventory trend data (monthly snapshots for 5 years)
        $this->generateInventoryTrendData($supplier);
        
        // Generate sales performance data (monthly sales for 5 years)
        $this->generateSalesPerformanceData($supplier);
        
        // Generate demand forecast data file
        $this->generateDemandForecastData();
        
        $this->command->info('Supplier analytics data generated successfully!');
    }
    
    private function generateInventoryTrendData($supplier)
    {
        $this->command->info('Generating inventory trend data...');
        
        $products = Product::where('supplier_id', $supplier->id)->get();
        $warehouses = Warehouse::all();
        
        if ($products->isEmpty() || $warehouses->isEmpty()) {
            $this->command->error('No products or warehouses found for inventory data');
            return;
        }
        
        $startDate = Carbon::now()->subYears(5)->startOfMonth();
        $endDate = Carbon::now();
        
        // Generate monthly inventory snapshots with realistic trends
        $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);
        
        foreach ($period as $date) {
            foreach ($products as $product) {
                foreach ($warehouses as $warehouse) {
                    // Generate realistic inventory levels with seasonal variations
                    $baseQuantity = rand(500, 2000);
                    
                    // Add seasonal variation (higher in harvest months)
                    $month = $date->month;
                    $seasonalMultiplier = 1.0;
                    if (in_array($month, [3, 4, 5, 6])) { // Harvest months
                        $seasonalMultiplier = 1.5;
                    } elseif (in_array($month, [12, 1, 2])) { // Low season
                        $seasonalMultiplier = 0.7;
                    }
                    
                    // Add growth trend over time
                    $monthsSinceStart = $startDate->diffInMonths($date);
                    $growthMultiplier = 1 + ($monthsSinceStart * 0.02); // 2% monthly growth
                    
                    $quantityOnHand = (int) ($baseQuantity * $seasonalMultiplier * $growthMultiplier);
                    $quantityReserved = (int) ($quantityOnHand * 0.2);
                    $quantityAvailable = $quantityOnHand - $quantityReserved;
                    $quantityOnOrder = (int) ($quantityOnHand * 0.3);
                    
                    // Create or update inventory record
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
                            'average_cost' => $product->cost_price + (rand(-10, 10) / 100),
                            'location' => 'A' . rand(1, 10) . '-' . rand(1, 20),
                            'expiry_date' => $date->addMonths(rand(6, 24)),
                            'status' => 'active',
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]
                    );
                }
            }
        }
        
        $this->command->info('Inventory trend data generated');
    }
    
    private function generateSalesPerformanceData($supplier)
    {
        $this->command->info('Generating sales performance data...');
        
        $products = Product::where('supplier_id', $supplier->id)->get();
        $customers = User::whereIn('role', ['manufacturer', 'retailer', 'distributor'])->get();
        $warehouses = Warehouse::all();
        
        if ($products->isEmpty() || $customers->isEmpty()) {
            $this->command->error('No products or customers found for sales data');
            return;
        }
        
        $startDate = Carbon::now()->subYears(5)->startOfMonth();
        $endDate = Carbon::now();
        
        // Generate monthly sales with realistic patterns
        $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);
        
        foreach ($period as $date) {
            // Generate 3-8 orders per month
            $ordersThisMonth = rand(3, 8);
            
            for ($i = 0; $i < $ordersThisMonth; $i++) {
                $customer = $customers->random();
                $productsForOrder = $products->random(rand(1, 3));
                
                $subtotal = 0;
                $orderItems = [];
                
                foreach ($productsForOrder as $product) {
                    $quantity = rand(50, 500);
                    $unitPrice = $product->unit_price + (rand(-5, 15) / 100);
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
                $shippingAmount = rand(500, 2000);
                $discountAmount = $subtotal * (rand(0, 10) / 100);
                $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;
                
                $order = Order::create([
                    'order_number' => 'SALES-' . strtoupper(uniqid()),
                    'customer_id' => $customer->id,
                    'vendor_id' => $supplier->id,
                    'order_type' => 'sale',
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
        
        $this->command->info('Sales performance data generated');
    }
    
    private function generateDemandForecastData()
    {
        $this->command->info('Generating AI demand forecast data...');
        
        // Create realistic demand forecast data
        $forecastData = [
            'title' => 'AI-Powered Wheat Demand Forecast',
            'description' => 'Machine learning analysis of historical patterns and market trends',
            'historical_dates' => [],
            'historical_values' => [],
            'forecast_dates' => [],
            'forecast_values' => [],
            'recommendations' => [
                'Increase wheat processing capacity by 15% in Q2 2025',
                'Plan for 850 tons average monthly processing',
                'Total expected demand: 5100 tons over next 6 months',
                'Consider expanding storage facilities in Nairobi region',
                'Monitor weather patterns for harvest season planning'
            ]
        ];
        
        // Generate historical data (last 24 months)
        $startDate = Carbon::now()->subMonths(24);
        for ($i = 0; $i < 24; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $forecastData['historical_dates'][] = $date->format('M Y');
            
            // Generate realistic historical demand with seasonal patterns
            $baseDemand = 600;
            $month = $date->month;
            
            // Seasonal variation
            if (in_array($month, [3, 4, 5, 6])) { // Harvest months
                $demand = $baseDemand * 1.3;
            } elseif (in_array($month, [12, 1, 2])) { // Low season
                $demand = $baseDemand * 0.8;
            } else {
                $demand = $baseDemand;
            }
            
            // Add some random variation
            $demand += rand(-50, 100);
            $forecastData['historical_values'][] = (int) $demand;
        }
        
        // Generate forecast data (next 12 months)
        $forecastStart = Carbon::now()->addMonth();
        for ($i = 0; $i < 12; $i++) {
            $date = $forecastStart->copy()->addMonths($i);
            $forecastData['forecast_dates'][] = $date->format('M Y');
            
            // Generate forecast with growth trend and seasonal patterns
            $baseForecast = 700 + ($i * 20); // Growing trend
            $month = $date->month;
            
            // Seasonal variation for forecast
            if (in_array($month, [3, 4, 5, 6])) {
                $forecast = $baseForecast * 1.4;
            } elseif (in_array($month, [12, 1, 2])) {
                $forecast = $baseForecast * 0.85;
            } else {
                $forecast = $baseForecast;
            }
            
            // Add some uncertainty to forecast
            $forecast += rand(-30, 60);
            $forecastData['forecast_values'][] = (int) $forecast;
        }
        
        // Save to JSON file
        $filePath = storage_path('app/demand_forecast_data.json');
        file_put_contents($filePath, json_encode($forecastData, JSON_PRETTY_PRINT));
        
        $this->command->info('AI demand forecast data saved to: ' . $filePath);
    }
} 