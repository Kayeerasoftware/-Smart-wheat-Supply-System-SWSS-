<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class ExportUserOrderHistory extends Command
{
    protected $signature = 'export:user-order-history';
    protected $description = 'Export each user\'s order history to CSV files for ML forecasting';

    public function handle()
    {
        $roles = ['farmer', 'supplier', 'manufacturer', 'distributor'];
        $exportPath = storage_path('app/ml_exports');
        if (!is_dir($exportPath)) {
            mkdir($exportPath, 0777, true);
        }

        foreach ($roles as $role) {
            $users = User::where('role', $role)->get();
            foreach ($users as $user) {
                $orders = Order::where('customer_id', $user->id)
                    ->with('orderItems')
                    ->get();
                $csvFile = $exportPath . "/{$role}_{$user->id}_orders.csv";
                $handle = fopen($csvFile, 'w');
                // Header
                fputcsv($handle, ['order_id', 'order_date', 'product_id', 'quantity', 'total_amount']);
                foreach ($orders as $order) {
                    foreach ($order->orderItems as $item) {
                        fputcsv($handle, [
                            $order->id,
                            $order->order_date,
                            $item->product_id,
                            $item->quantity,
                            $order->total_amount
                        ]);
                    }
                }
                fclose($handle);
            }
        }
        $this->info('User order histories exported successfully.');
    }
} 