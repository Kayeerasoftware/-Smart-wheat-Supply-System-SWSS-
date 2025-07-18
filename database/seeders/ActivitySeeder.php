<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $supplier = User::where('email', 'supplier@gmail.com')->first();
        if (!$supplier) {
            $this->command->warn('Supplier not found. Please run SupplierUserSeeder first.');
            return;
        }
        $startDate = now()->subYears(5)->startOfMonth();
        $endDate = now();
        $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);
        $activityTypes = ['login', 'inventory_update', 'order_placement'];
        foreach ($period as $date) {
            foreach ($activityTypes as $type) {
                Activity::create([
                    'user_id' => $supplier->id,
                    'type' => $type,
                    'description' => ucfirst(str_replace('_', ' ', $type)) . ' by supplier',
                    'created_at' => $date->copy()->addDays(rand(0, 27)),
                    'updated_at' => $date->copy()->addDays(rand(0, 27)),
                ]);
            }
        }
        $this->command->info('5 years of monthly supplier activity logs created.');
    }
} 