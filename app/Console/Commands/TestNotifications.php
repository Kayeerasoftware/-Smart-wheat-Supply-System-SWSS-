<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\FacilityVisit;
use App\Services\NotificationService;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {type=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification system by sending sample notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        
        // Get a supplier user for testing
        $supplier = User::where('role', 'supplier')->first();
        if (!$supplier) {
            $this->error('No supplier user found. Please create a supplier user first.');
            return 1;
        }

        // Get an admin user for testing
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->error('No admin user found. Please create an admin user first.');
            return 1;
        }

        if ($type === 'all' || $type === 'visit') {
            $this->testFacilityVisitNotifications($supplier);
        }

        if ($type === 'all' || $type === 'chat') {
            $this->testChatNotifications($supplier, $admin);
        }

        $this->info('Notification tests completed successfully!');
        return 0;
    }

    private function testFacilityVisitNotifications($supplier)
    {
        $this->info('Testing facility visit notifications...');

        // Create a sample facility visit
        $visit = FacilityVisit::create([
            'vendor_id' => $supplier->vendor->id ?? 1,
            'scheduled_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'status' => 'scheduled',
            'notes' => 'Sample facility visit for testing notifications'
        ]);

        // Send different types of visit notifications
        $types = ['scheduled', 'rescheduled', 'completed', 'cancelled'];
        
        foreach ($types as $type) {
            $this->info("Sending {$type} notification...");
            NotificationService::sendFacilityVisitNotification($visit, $type);
            sleep(1); // Small delay between notifications
        }
    }

    private function testChatNotifications($supplier, $admin)
    {
        $this->info('Testing chat notifications...');

        $messages = [
            'Hello! I have a question about my application.',
            'When will my facility visit be scheduled?',
            'Thank you for the quick response!',
            'I need to update my business information.'
        ];

        foreach ($messages as $index => $message) {
            $this->info("Sending chat notification " . ($index + 1) . "...");
            
            if ($index % 2 === 0) {
                // Supplier sending to admin
                NotificationService::sendChatNotification($supplier, $admin, $message, 'admin');
            } else {
                // Admin sending to supplier
                NotificationService::sendChatNotification($admin, $supplier, $message, 'direct');
            }
            
            sleep(1); // Small delay between notifications
        }
    }
}
