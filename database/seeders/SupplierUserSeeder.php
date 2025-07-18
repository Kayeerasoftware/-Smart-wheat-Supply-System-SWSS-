<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SupplierUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'supplier@gmail.com'],
            [
                'username' => 'main_supplier',
                'email' => 'supplier@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'supplier',
                'phone' => '+1-555-0201',
                'address' => '123 Supplier Lane, Supply City, SC 12345',
                'status' => 'active',
            ]
        );
    }
} 