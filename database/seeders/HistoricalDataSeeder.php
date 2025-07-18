<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Shipment;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class HistoricalDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating 5 years of historical data...');
        
        // Generate users with realistic registration dates
        $this->generateHistoricalUsers();
        
        // Generate products and categories
        $this->generateProducts();
        
        // Generate warehouses
        $this->generateWarehouses();
        
        // Generate inventory movements
        $this->generateInventoryHistory();
        
        // Generate orders and purchase orders
        $this->generateOrderHistory();
        
        // Generate shipments
        $this->generateShipmentHistory();
        
        $this->command->info('Historical data generation completed!');
    }
    
    private function generateHistoricalUsers()
    {
        $this->command->info('Generating historical users...');
        
        $roles = ['farmer', 'supplier', 'manufacturer', 'distributor', 'retailer'];
        $cities = ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Kakamega', 'Nyeri'];
        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Mary', 'James', 'Elizabeth', 'Robert', 'Linda', 'William', 'Barbara', 'Richard', 'Susan', 'Joseph', 'Jessica', 'Thomas', 'Sarah', 'Christopher', 'Karen'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin'];
        
        // Create users over the past 5 years with realistic registration patterns
        $startDate = Carbon::now()->subYears(5);
        $endDate = Carbon::now();
        
        // Generate 50-100 users with realistic registration dates
        $userCount = rand(50, 100);
        
        for ($i = 0; $i < $userCount; $i++) {
            // Random registration date with more recent users being more common
            $registrationDate = $this->getRandomDateWithTrend($startDate, $endDate, 0.7);
            
            $role = $roles[array_rand($roles)];
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $city = $cities[array_rand($cities)];
            
            $user = User::create([
                'username' => strtolower($firstName . $lastName . rand(1, 999)),
                'email' => strtolower($firstName . $lastName . rand(1, 999)) . '@example.com',
                'password' => Hash::make('password'),
                'role' => $role,
                'phone' => '+254' . rand(700000000, 799999999),
                'address' => rand(1, 999) . ' ' . $city . ' Street, ' . $city . ', Kenya',
                'status' => 'active',
                'created_at' => $registrationDate,
                'updated_at' => $registrationDate,
            ]);
            
            // Create vendor record for suppliers
            if ($role === 'supplier') {
                Vendor::create([
                    'user_id' => $user->id,
                    'status' => 'approved',
                    'score_financial' => rand(70, 95),
                    'score_reputation' => rand(75, 98),
                    'score_compliance' => rand(80, 100),
                    'total_score' => rand(75, 95),
                    'created_at' => $registrationDate,
                    'updated_at' => $registrationDate,
                ]);
            }
        }
        
        $this->command->info("Created {$userCount} historical users");
    }
    
    private function generateProducts()
    {
        $this->command->info('Generating products...');
        
        $categories = [
            ['name' => 'Raw Wheat', 'description' => 'Unprocessed wheat grains'],
            ['name' => 'Organic Wheat', 'description' => 'Certified organic wheat'],
            ['name' => 'Flour Products', 'description' => 'Processed flour varieties'],
            ['name' => 'Bakery Products', 'description' => 'Fresh baked goods'],
            ['name' => 'Packaging Materials', 'description' => 'Packaging supplies'],
            ['name' => 'Seeds', 'description' => 'Agricultural seeds'],
            ['name' => 'Fertilizers', 'description' => 'Agricultural fertilizers'],
            ['name' => 'Equipment', 'description' => 'Farming equipment'],
        ];
        
        foreach ($categories as $catData) {
            Category::firstOrCreate(
                ['name' => $catData['name']],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'slug' => Str::slug($catData['name']),
                    'is_active' => true,
                ]
            );
        }
        
        $products = [
            ['name' => 'Wheat Grain', 'category' => 'Raw Wheat', 'unit' => 'kg', 'price' => 2.00, 'cost' => 1.50, 'type' => 'raw'],
            ['name' => 'Organic Wheat', 'category' => 'Organic Wheat', 'unit' => 'kg', 'price' => 3.20, 'cost' => 2.40, 'type' => 'raw'],
            ['name' => 'Bread Flour', 'category' => 'Flour Products', 'unit' => 'kg', 'price' => 3.00, 'cost' => 2.10, 'type' => 'processed'],
            ['name' => 'Cake Flour', 'category' => 'Flour Products', 'unit' => 'kg', 'price' => 3.50, 'cost' => 2.60, 'type' => 'processed'],
            ['name' => 'Fresh Bread', 'category' => 'Bakery Products', 'unit' => 'pieces', 'price' => 4.50, 'cost' => 2.80, 'type' => 'processed'],
            ['name' => 'Wheat Bags', 'category' => 'Packaging Materials', 'unit' => 'pieces', 'price' => 0.50, 'cost' => 0.30, 'type' => 'raw'],
            ['name' => 'Wheat Seeds', 'category' => 'Seeds', 'unit' => 'kg', 'price' => 5.00, 'cost' => 3.50, 'type' => 'raw'],
            ['name' => 'NPK Fertilizer', 'category' => 'Fertilizers', 'unit' => 'kg', 'price' => 2.50, 'cost' => 1.80, 'type' => 'raw'],
        ];
        
        $suppliers = User::where('role', 'supplier')->get();
        
        foreach ($products as $prodData) {
            $category = Category::where('name', $prodData['category'])->first();
            $supplier = $suppliers->random();
            
            if ($category) {
                Product::firstOrCreate(
                    ['name' => $prodData['name']],
                    [
                        'name' => $prodData['name'],
                        'sku' => 'SKU-' . strtoupper(Str::random(8)),
                        'category_id' => $category->id,
                        'supplier_id' => $supplier->id,
                        'brand' => 'Farm Fresh',
                        'description' => 'High-quality ' . strtolower($prodData['name']),
                        'unit_of_measure' => $prodData['unit'],
                        'unit_price' => $prodData['price'],
                        'cost_price' => $prodData['cost'],
                        'reorder_point' => rand(50, 200),
                        'reorder_quantity' => rand(200, 1000),
                        'is_raw_material' => $prodData['type'] === 'raw',
                        'is_finished_good' => $prodData['type'] === 'processed',
                        'status' => 'active',
                        'type' => $prodData['type'],
                    ]
                );
            }
        }
        
        $this->command->info('Products generated');
    }
    
    private function generateWarehouses()
    {
        $this->command->info('Generating warehouses...');
        
        $warehouses = [
            ['name' => 'Main Distribution Center', 'city' => 'Nairobi', 'type' => 'distribution'],
            ['name' => 'Coastal Storage Facility', 'city' => 'Mombasa', 'type' => 'finished_goods'],
            ['name' => 'Western Regional Hub', 'city' => 'Kisumu', 'type' => 'distribution'],
            ['name' => 'Rift Valley Storage', 'city' => 'Nakuru', 'type' => 'raw_materials'],
            ['name' => 'Cold Storage Facility', 'city' => 'Eldoret', 'type' => 'cold_storage'],
        ];
        
        foreach ($warehouses as $warehouseData) {
            Warehouse::firstOrCreate(
                ['name' => $warehouseData['name']],
                [
                    'name' => $warehouseData['name'],
                    'description' => 'Storage facility in ' . $warehouseData['city'],
                    'code' => 'WH-' . strtoupper(substr($warehouseData['city'], 0, 3)),
                    'address' => rand(1, 999) . ' Industrial Road',
                    'city' => $warehouseData['city'],
                    'state' => 'Kenya',
                    'country' => 'Kenya',
                    'postal_code' => rand(10000, 99999),
                    'phone' => '+254' . rand(700000000, 799999999),
                    'email' => 'warehouse@' . strtolower($warehouseData['city']) . '.com',
                    'capacity' => rand(5000, 50000),
                    'capacity_unit' => 'sq_meters',
                    'type' => $warehouseData['type'],
                    'is_active' => true,
                ]
            );
        }
        
        $this->command->info('Warehouses generated');
    }
    
    private function generateInventoryHistory()
    {
        $this->command->info('Generating inventory history...');
        
        $products = Product::all();
        $warehouses = Warehouse::all();
        $startDate = Carbon::now()->subYears(5);
        $endDate = Carbon::now();
        
        // Generate monthly inventory snapshots
        $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);
        
        foreach ($period as $date) {
            foreach ($products as $product) {
                foreach ($warehouses as $warehouse) {
                    // Random inventory levels with some realistic variation
                    $quantityOnHand = rand(0, 1000);
                    $quantityReserved = rand(0, $quantityOnHand * 0.3);
                    $quantityAvailable = $quantityOnHand - $quantityReserved;
                    $quantityOnOrder = rand(0, 500);
                    
                    Inventory::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                        'quantity_on_hand' => $quantityOnHand,
                        'quantity_reserved' => $quantityReserved,
                        'quantity_available' => $quantityAvailable,
                        'quantity_on_order' => $quantityOnOrder,
                        'average_cost' => $product->cost_price + (rand(-20, 20) / 100),
                        'location' => 'A' . rand(1, 10) . '-' . rand(1, 20),
                        'batch_number' => 'BATCH-' . $date->format('Y-m') . '-' . rand(1000, 9999),
                        'expiry_date' => $date->addMonths(rand(6, 24)),
                        'status' => 'active',
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }
            }
        }
        
        $this->command->info('Inventory history generated');
    }
    
    private function generateOrderHistory()
    {
        $this->command->info('Generating order history...');
        
        $suppliers = User::where('role', 'supplier')->get();
        $farmers = User::where('role', 'farmer')->get();
        $manufacturers = User::where('role', 'manufacturer')->get();
        $retailers = User::where('role', 'retailer')->get();
        $products = Product::all();
        $warehouses = Warehouse::all();
        
        $startDate = Carbon::now()->subYears(5);
        $endDate = Carbon::now();
        
        // Generate orders with realistic patterns (more orders in recent years)
        $totalOrders = rand(500, 1000);
        
        for ($i = 0; $i < $totalOrders; $i++) {
            $orderDate = $this->getRandomDateWithTrend($startDate, $endDate, 0.6);
            
            // Determine order type and participants
            $orderType = rand(1, 10) <= 7 ? 'purchase' : 'sale';
            
            if ($orderType === 'purchase') {
                // Supplier buying from farmer
                $customer = $suppliers->random();
                $vendor = null; // Farmers are not vendors in this system
                $productsForOrder = $products->where('type', 'raw')->random(rand(1, 3));
            } else {
                // Supplier selling to manufacturer/retailer
                $customer = collect([$manufacturers, $retailers])->flatten()->random();
                $vendor = $suppliers->random();
                $productsForOrder = $products->where('type', 'processed')->random(rand(1, 3));
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
                'expected_delivery_date' => $orderDate->addDays(rand(3, 14)),
                'actual_delivery_date' => $status === 'delivered' ? $orderDate->addDays(rand(1, 10)) : null,
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
        }
        
        $this->command->info("Generated {$totalOrders} historical orders");
    }
    
    private function generateShipmentHistory()
    {
        $this->command->info('Generating shipment history...');
        
        $orders = Order::where('status', '!=', 'draft')->get();
        $warehouses = Warehouse::all();
        
        foreach ($orders as $order) {
            if (rand(1, 10) <= 8) { // 80% of orders have shipments
                $shipmentDate = $order->created_at->addDays(rand(1, 5));
                
                Shipment::create([
                    'shipment_number' => 'SHP-' . strtoupper(Str::random(10)),
                    'order_id' => $order->id,
                    'warehouse_id' => $warehouses->random()->id,
                    'shipment_type' => 'outbound',
                    'status' => $order->status === 'delivered' ? 'delivered' : 'shipped',
                    'carrier' => $order->carrier,
                    'tracking_number' => $order->tracking_number,
                    'shipping_method' => ['standard', 'express', 'overnight'][array_rand(['standard', 'express', 'overnight'])],
                    'shipping_cost' => $order->shipping_amount,
                    'insurance_amount' => $order->total_amount * 0.02,
                    'shipping_address' => $order->shipping_address,
                    'billing_address' => $order->billing_address,
                    'ship_date' => $shipmentDate,
                    'expected_delivery_date' => $order->expected_delivery_date,
                    'actual_delivery_date' => $order->actual_delivery_date,
                    'notes' => 'Historical shipment',
                    'signature_required' => 'yes',
                    'created_at' => $shipmentDate,
                    'updated_at' => $shipmentDate,
                ]);
            }
        }
        
        $this->command->info('Shipment history generated');
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