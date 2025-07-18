<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoricalActivitySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating historical activity logs...');
        
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->error('No users found for activity logs');
            return;
        }
        
        $startDate = Carbon::now()->subYears(5);
        $endDate = Carbon::now();
        
        // Generate activity logs with realistic patterns
        $totalActivities = rand(1000, 2000);
        $activitiesCreated = 0;
        
        $activityTypes = [
            'user_login',
            'order_created',
            'order_updated',
            'product_viewed',
            'inventory_updated',
            'shipment_created',
            'message_sent',
            'report_generated',
            'payment_processed',
            'user_registered',
        ];
        
        $activityDescriptions = [
            'user_login' => 'User logged into the system',
            'order_created' => 'New order was created',
            'order_updated' => 'Order status was updated',
            'product_viewed' => 'Product details were viewed',
            'inventory_updated' => 'Inventory levels were updated',
            'shipment_created' => 'New shipment was created',
            'message_sent' => 'Message was sent to another user',
            'report_generated' => 'Analytics report was generated',
            'payment_processed' => 'Payment was processed successfully',
            'user_registered' => 'New user registered in the system',
        ];
        
        for ($i = 0; $i < $totalActivities; $i++) {
            try {
                // Generate date with trend towards recent dates
                $activityDate = $this->getRandomDateWithTrend($startDate, $endDate, 0.8);
                
                $user = $users->random();
                $activityType = $activityTypes[array_rand($activityTypes)];
                
                // Insert directly into database since we don't have an Activity model
                DB::table('activity_logs')->insert([
                    'user_id' => $user->id,
                    'activity_type' => $activityType,
                    'description' => $activityDescriptions[$activityType],
                    'ip_address' => $this->getRandomIP(),
                    'user_agent' => $this->getRandomUserAgent(),
                    'created_at' => $activityDate,
                    'updated_at' => $activityDate,
                ]);
                
                $activitiesCreated++;
                
                if ($activitiesCreated % 200 === 0) {
                    $this->command->info("Created {$activitiesCreated} activity logs...");
                }
                
            } catch (\Exception $e) {
                $this->command->warn("Error creating activity log {$i}: " . $e->getMessage());
                continue;
            }
        }
        
        $this->command->info("Successfully created {$activitiesCreated} historical activity logs!");
    }
    
    private function getRandomDateWithTrend($startDate, $endDate, $trend = 0.5)
    {
        // Generate dates with a trend towards more recent dates
        $daysDiff = $startDate->diffInDays($endDate);
        $randomDays = (int) ($daysDiff * pow(rand(0, 100) / 100, $trend));
        return $startDate->copy()->addDays($randomDays);
    }
    
    private function getRandomIP()
    {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255);
    }
    
    private function getRandomUserAgent()
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ];
        
        return $userAgents[array_rand($userAgents)];
    }
} 