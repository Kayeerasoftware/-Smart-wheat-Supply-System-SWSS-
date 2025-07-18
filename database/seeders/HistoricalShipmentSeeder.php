<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shipment;
use App\Models\Order;
use App\Models\Warehouse;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HistoricalShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating historical shipments...');
        
        $orders = Order::where('status', '!=', 'draft')->get();
        $warehouses = Warehouse::all();
        
        if ($orders->isEmpty() || $warehouses->isEmpty()) {
            $this->command->error('No orders or warehouses found for shipments');
            return;
        }
        
        $shipmentsCreated = 0;
        
        foreach ($orders as $order) {
            // 80% of orders have shipments
            if (rand(1, 10) <= 8) {
                try {
                    $shipmentDate = $order->created_at->addDays(rand(1, 5));
                    
                    $shipment = Shipment::create([
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
                        'notes' => 'Historical shipment for order ' . $order->order_number,
                        'signature_required' => 'yes',
                        'created_at' => $shipmentDate,
                        'updated_at' => $shipmentDate,
                    ]);
                    
                    $shipmentsCreated++;
                    
                    if ($shipmentsCreated % 50 === 0) {
                        $this->command->info("Created {$shipmentsCreated} shipments...");
                    }
                    
                } catch (\Exception $e) {
                    $this->command->warn("Error creating shipment for order {$order->id}: " . $e->getMessage());
                    continue;
                }
            }
        }
        
        $this->command->info("Successfully created {$shipmentsCreated} historical shipments!");
    }
} 