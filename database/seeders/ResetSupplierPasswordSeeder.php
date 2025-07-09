<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetSupplierPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset password for all supplier accounts
        $suppliers = User::where('role', 'supplier')->get();
        
        foreach ($suppliers as $supplier) {
            $supplier->update([
                'password' => Hash::make('password123'),
                'status' => 'active'
            ]);
            
            echo "Reset password for: " . $supplier->email . "\n";
        }
        
        echo "\nAll supplier accounts now have password: password123\n";
        echo "Try logging in with any supplier email and password: password123\n";
    }
} 