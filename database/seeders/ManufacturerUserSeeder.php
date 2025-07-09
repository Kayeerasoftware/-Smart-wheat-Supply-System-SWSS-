<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManufacturerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manufacturers = [
            [
                'username' => 'mike_factory_manager',
                'email' => 'mike.factory@manufacturing.com',
                'password' => Hash::make('password'),
                'role' => 'manufacturer',
                'phone' => '+1-555-0201',
                'address' => '123 Production Ave, Factory City, FC 12345',
                'status' => 'active'
            ],
            [
                'username' => 'production_co_quality',
                'email' => 'quality@productionco.com',
                'password' => Hash::make('password'),
                'role' => 'manufacturer',
                'phone' => '+1-555-0202',
                'address' => '456 Quality Street, Industrial Town, IT 67890',
                'status' => 'active'
            ],
            [
                'username' => 'wheat_processing_inc',
                'email' => 'contact@wheatprocessing.com',
                'password' => Hash::make('password'),
                'role' => 'manufacturer',
                'phone' => '+1-555-0203',
                'address' => '789 Processing Road, Mill City, MC 11111',
                'status' => 'active'
            ],
            [
                'username' => 'grain_tech_manufacturing',
                'email' => 'info@graintech.com',
                'password' => Hash::make('password'),
                'role' => 'manufacturer',
                'phone' => '+1-555-0204',
                'address' => '321 Tech Drive, Innovation City, IC 22222',
                'status' => 'active'
            ],
        ];

        foreach ($manufacturers as $manufacturer) {
            User::updateOrCreate(
                ['email' => $manufacturer['email']],
                $manufacturer
            );
        }
    }
} 