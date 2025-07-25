<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Wheat Suppliers Co.',
                'email' => 'contact@wheatsuppliers.com',
                'phone' => '+1-555-0123',
                'address' => '123 Wheat Street, Farm City, FC 12345',
                'status' => 'active',
            ],
            [
                'name' => 'Grain Masters Ltd.',
                'email' => 'info@grainmasters.com',
                'phone' => '+1-555-0456',
                'address' => '456 Grain Avenue, Harvest Town, HT 67890',
                'status' => 'active',
            ],
            [
                'name' => 'Premium Wheat Corp.',
                'email' => 'sales@premiumwheat.com',
                'phone' => '+1-555-0789',
                'address' => '789 Premium Road, Quality City, QC 11111',
                'status' => 'active',
            ],
            [
                'name' => 'Organic Wheat Partners',
                'email' => 'orders@organicwheat.com',
                'phone' => '+1-555-0124',
                'address' => '321 Organic Lane, Green Town, GT 22222',
                'status' => 'active',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
} 