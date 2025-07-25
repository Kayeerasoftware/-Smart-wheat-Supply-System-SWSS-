<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RawMaterial;
use App\Models\Supplier;

class RawMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();

        if ($suppliers->isEmpty()) {
            $this->command->warn('No suppliers found. Please run SupplierSeeder first.');
            return;
        }

        $rawMaterials = [
            [
                'name' => 'Premium Wheat Flour',
                'quantity' => 150,
                'unit' => 'kg',
                'supplier_id' => $suppliers->first()->id,
                'minimum_quantity' => 50,
                'reorder_point' => 75,
            ],
            [
                'name' => 'Organic Wheat Grain',
                'quantity' => 200,
                'unit' => 'kg',
                'supplier_id' => $suppliers->get(1)->id,
                'minimum_quantity' => 60,
                'reorder_point' => 80,
            ],
            [
                'name' => 'Whole Wheat Flour',
                'quantity' => 100,
                'unit' => 'kg',
                'supplier_id' => $suppliers->get(2)->id,
                'minimum_quantity' => 40,
                'reorder_point' => 60,
            ],
            [
                'name' => 'Durum Wheat',
                'quantity' => 15, // Low inventory for testing alerts
                'unit' => 'kg',
                'supplier_id' => $suppliers->get(3)->id,
                'minimum_quantity' => 30,
                'reorder_point' => 45,
            ],
            [
                'name' => 'Bread Flour',
                'quantity' => 80,
                'unit' => 'kg',
                'supplier_id' => $suppliers->first()->id,
                'minimum_quantity' => 25,
                'reorder_point' => 40,
            ],
        ];

        foreach ($rawMaterials as $material) {
            RawMaterial::create($material);
        }
    }
} 