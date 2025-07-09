<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use Carbon\Carbon;

class GenerateSampleNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:generate-samples';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sample notifications for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sample notifications...');

        // Get all supplier users
        $suppliers = User::where('role', 'supplier')->get();

        if ($suppliers->isEmpty()) {
            $this->error('No supplier users found. Please create some supplier users first.');
            return 1;
        }

        foreach ($suppliers as $supplier) {
            $this->generateNotificationsForUser($supplier);
        }

        $this->info('Sample notifications generated successfully!');
        return 0;
    }

    private function generateNotificationsForUser(User $user)
    {
        $this->line("Generating notifications for user: {$user->name}");

        // Report Ready Notification
        $user->notify(new \App\Notifications\ReportReadyNotification('daily_summary', [
            'date' => Carbon::now()->format('Y-m-d'),
            'total_orders' => rand(5, 25),
            'total_inventory' => rand(100, 500),
        ]));

        // Order Status Notification
        $user->notify(new \App\Notifications\OrderStatusNotification(
            new \App\Models\Order(['id' => rand(1, 100), 'order_number' => 'ORD-' . rand(1000, 9999)]),
            'confirmed'
        ));

        // Inventory Alert Notification
        $inventory = new \App\Models\Inventory([
            'id' => rand(1, 50),
            'quantity_available' => rand(1, 15)
        ]);
        $inventory->product = new \App\Models\Product(['name' => 'Sample Wheat Product']);
        
        $user->notify(new \App\Notifications\InventoryAlertNotification($inventory, 'low_stock'));

        // System Notification
        $user->notify(new \App\Notifications\SystemNotification(
            'System Maintenance',
            'Scheduled maintenance will occur tonight at 2 AM. Please save your work.',
            'warning'
        ));

        // Chat Notification
        $user->notify(new \App\Notifications\ChatNotification(
            new User(['name' => 'Admin User']),
            'Hello! How can I help you today?',
            'direct'
        ));

        $this->line("Generated 5 notifications for {$user->name}");
    }
}
