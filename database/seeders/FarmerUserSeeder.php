<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FarmerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $farmers = [
            [
                'username' => 'john_doe_farmer',
                'email' => 'john.doe@farm.com',
                'password' => Hash::make('password'),
                'role' => 'farmer',
                'phone' => '+1-555-0101',
                'address' => '123 Wheat Field Road, Farm City, FC 12345',
                'status' => 'active'
            ],
            [
                'username' => 'jane_smith_farmer',
                'email' => 'jane.smith@farm.com',
                'password' => Hash::make('password'),
                'role' => 'farmer',
                'phone' => '+1-555-0102',
                'address' => '456 Organic Farm Lane, Green Town, GT 67890',
                'status' => 'active'
            ],
            [
                'username' => 'mike_wilson_farmer',
                'email' => 'mike.wilson@farm.com',
                'password' => Hash::make('password'),
                'role' => 'farmer',
                'phone' => '+1-555-0103',
                'address' => '789 Harvest Drive, Crop City, CC 11111',
                'status' => 'active'
            ],
            [
                'username' => 'sarah_brown_farmer',
                'email' => 'sarah.brown@farm.com',
                'password' => Hash::make('password'),
                'role' => 'farmer',
                'phone' => '+1-555-0104',
                'address' => '321 Grain Valley, Wheat Town, WT 22222',
                'status' => 'active'
            ],
            [
                'username' => 'david_lee_farmer',
                'email' => 'david.lee@farm.com',
                'password' => Hash::make('password'),
                'role' => 'farmer',
                'phone' => '+1-555-0105',
                'address' => '654 Farm Road, Rural City, RC 33333',
                'status' => 'active'
            ],
        ];

        foreach ($farmers as $farmer) {
            User::updateOrCreate(
                ['email' => $farmer['email']],
                $farmer
            );
        }
    }
} 