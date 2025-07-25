<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RetailerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $retailers = [
            [
                'username' => 'retailer1',
                'email' => 'retailer1@example.com',
                'password' => Hash::make('password'),
                'role' => 'retailer',
                'phone' => '+1234567890',
                'address' => '123 Retail St, City, State 12345',
                'status' => 'active'
            ],
            [
                'username' => 'retailer2',
                'email' => 'retailer2@example.com',
                'password' => Hash::make('password'),
                'role' => 'retailer',
                'phone' => '+1234567891',
                'address' => '456 Store Ave, City, State 12345',
                'status' => 'active'
            ],
            [
                'username' => 'retailer3',
                'email' => 'retailer3@example.com',
                'password' => Hash::make('password'),
                'role' => 'retailer',
                'phone' => '+1234567892',
                'address' => '789 Market Blvd, City, State 12345',
                'status' => 'active'
            ],
        ];

        foreach ($retailers as $retailer) {
            User::updateOrCreate(
                ['email' => $retailer['email']],
                $retailer
            );
        }
    }
} 