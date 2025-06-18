<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductionLine;

class ProductionLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productionLines = [
            [
                'name' => 'Production Line A',
                'capacity' => 1000,
                'status' => 'active',
            ],
            [
                'name' => 'Production Line B',
                'capacity' => 800,
                'status' => 'active',
            ],
            [
                'name' => 'Production Line C',
                'capacity' => 1200,
                'status' => 'maintenance',
            ],
            [
                'name' => 'Production Line D',
                'capacity' => 600,
                'status' => 'inactive',
            ],
        ];

        foreach ($productionLines as $line) {
            ProductionLine::create($line);
        }
    }
} 