<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;

class TestSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test supplier user with the email the user is trying to use
        $supplier = User::updateOrCreate(
            ['email' => 'supplier@gmail.com'],
            [
                'username' => 'supplier',
                'email' => 'supplier@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'supplier',
                'phone' => '+1234567890',
                'address' => '123 Test Street, Test City, TC 12345',
                'status' => 'active'
            ]
        );

        // Create a vendor record for the supplier
        Vendor::updateOrCreate(
            ['user_id' => $supplier->id],
            [
                'user_id' => $supplier->id,
                'application_data' => [
                    'company_name' => 'Test Supplier Company',
                    'business_type' => 'Corporation',
                    'years_in_business' => 5,
                    'annual_revenue' => 1000000,
                    'number_of_employees' => 50,
                    'certifications' => ['ISO 9001', 'HACCP'],
                    'facilities' => [
                        [
                            'name' => 'Main Warehouse',
                            'address' => '123 Test Street, Test City, TC 12345',
                            'size' => '10,000 sq ft',
                            'equipment' => ['Forklifts', 'Conveyor Systems', 'Climate Control']
                        ]
                    ]
                ],
                'status' => 'approved',
                'score_financial' => 85,
                'score_reputation' => 90,
                'score_compliance' => 88,
                'total_score' => 87.7,
                'pdf_paths' => [
                    'business_license' => 'documents/business_license.pdf',
                    'tax_certificate' => 'documents/tax_certificate.pdf',
                    'insurance_certificate' => 'documents/insurance_certificate.pdf'
                ]
            ]
        );

        echo "Test supplier created:\n";
        echo "Username: supplier\n";
        echo "Email: supplier@gmail.com\n";
        echo "Password: password123\n";
    }
} 