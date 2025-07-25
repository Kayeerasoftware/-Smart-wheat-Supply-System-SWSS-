<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DistributorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distributors = [
            [
                'username' => 'midwest_distributors',
                'email' => 'contact@midwestdistributors.com',
                'password' => Hash::make('password'),
                'role' => 'distributor',
                'phone' => '+1-555-0301',
                'address' => '123 Distribution Center, Logistics City, LC 12345',
                'status' => 'active'
            ],
            [
                'username' => 'grain_express_logistics',
                'email' => 'info@grainexpress.com',
                'password' => Hash::make('password'),
                'role' => 'distributor',
                'phone' => '+1-555-0302',
                'address' => '456 Express Way, Transport Town, TT 67890',
                'status' => 'active'
            ],
            [
                'username' => 'wheat_flow_distribution',
                'email' => 'sales@wheatflow.com',
                'password' => Hash::make('password'),
                'role' => 'distributor',
                'phone' => '+1-555-0303',
                'address' => '789 Flow Street, Supply City, SC 11111',
                'status' => 'active'
            ],
        ];

        foreach ($distributors as $distributor) {
            User::updateOrCreate(
                ['email' => $distributor['email']],
                $distributor
            );
        }
    }
} 