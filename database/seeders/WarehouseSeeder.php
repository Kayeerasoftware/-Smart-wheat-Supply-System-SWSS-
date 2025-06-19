<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Main Distribution Center',
                'code' => 'MDC001',
                'description' => 'Primary distribution center for wheat products',
                'address' => '123 Main Street',
                'city' => 'Kansas City',
                'state' => 'Missouri',
                'postal_code' => '64101',
                'country' => 'USA',
                'phone' => '+1-555-0123',
                'email' => 'mdc@swss.com',
                'manager_name' => 'John Smith',
                'capacity' => 100000,
                'is_active' => true,
            ],
            [
                'name' => 'West Coast Warehouse',
                'code' => 'WCW002',
                'description' => 'Warehouse serving the West Coast region',
                'address' => '456 Pacific Avenue',
                'city' => 'Los Angeles',
                'state' => 'California',
                'postal_code' => '90210',
                'country' => 'USA',
                'phone' => '+1-555-0456',
                'email' => 'wcw@swss.com',
                'manager_name' => 'Sarah Johnson',
                'capacity' => 75000,
                'is_active' => true,
            ],
            [
                'name' => 'East Coast Storage',
                'code' => 'ECS003',
                'description' => 'Storage facility for East Coast distribution',
                'address' => '789 Atlantic Boulevard',
                'city' => 'New York',
                'state' => 'New York',
                'postal_code' => '10001',
                'country' => 'USA',
                'phone' => '+1-555-0789',
                'email' => 'ecs@swss.com',
                'manager_name' => 'Michael Brown',
                'capacity' => 80000,
                'is_active' => true,
            ],
            [
                'name' => 'Processing Plant Warehouse',
                'code' => 'PPW004',
                'description' => 'Warehouse attached to wheat processing plant',
                'address' => '321 Industrial Drive',
                'city' => 'Chicago',
                'state' => 'Illinois',
                'postal_code' => '60601',
                'country' => 'USA',
                'phone' => '+1-555-0321',
                'email' => 'ppw@swss.com',
                'manager_name' => 'Lisa Davis',
                'capacity' => 60000,
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $warehouseData) {
            Warehouse::create($warehouseData);
        }
    }
}
