<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;

class HistoricalMessageSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating historical messages...');
        
        $users = User::all();
        
        if ($users->count() < 2) {
            $this->command->error('Need at least 2 users for messages');
            return;
        }
        
        $startDate = Carbon::now()->subYears(5);
        $endDate = Carbon::now();
        
        // Generate messages with realistic patterns
        $totalMessages = rand(500, 1000);
        $messagesCreated = 0;
        
        $messageTemplates = [
            'Hello, I would like to discuss a potential order for wheat products.',
            'Thank you for your recent order. It has been processed and will be shipped soon.',
            'Could you please provide a quote for 1000kg of organic wheat?',
            'The delivery has been scheduled for next week. Please confirm the address.',
            'We have received your payment. Thank you for your business!',
            'I need to discuss the quality specifications for the flour order.',
            'The shipment is in transit and should arrive within 2-3 business days.',
            'Please let me know if you have any questions about the pricing.',
            'We appreciate your continued partnership with our company.',
            'Could you update me on the status of my recent order?',
            'I would like to place a bulk order for next month.',
            'Thank you for the excellent service and product quality.',
            'Do you have any special discounts for large orders?',
            'The product quality has been excellent. We will order again.',
            'Please send the invoice for the recent transaction.',
        ];
        
        for ($i = 0; $i < $totalMessages; $i++) {
            try {
                // Generate date with trend towards recent dates
                $messageDate = $this->getRandomDateWithTrend($startDate, $endDate, 0.7);
                
                $sender = $users->random();
                $receiver = $users->where('id', '!=', $sender->id)->random();
                
                $message = Message::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'message' => $messageTemplates[array_rand($messageTemplates)],
                    'is_read' => rand(1, 10) <= 8, // 80% of messages are read
                    'created_at' => $messageDate,
                    'updated_at' => $messageDate,
                ]);
                
                $messagesCreated++;
                
                if ($messagesCreated % 100 === 0) {
                    $this->command->info("Created {$messagesCreated} messages...");
                }
                
            } catch (\Exception $e) {
                $this->command->warn("Error creating message {$i}: " . $e->getMessage());
                continue;
            }
        }
        
        $this->command->info("Successfully created {$messagesCreated} historical messages!");
    }
    
    private function getRandomDateWithTrend($startDate, $endDate, $trend = 0.5)
    {
        // Generate dates with a trend towards more recent dates
        $daysDiff = $startDate->diffInDays($endDate);
        $randomDays = (int) ($daysDiff * pow(rand(0, 100) / 100, $trend));
        return $startDate->copy()->addDays($randomDays);
    }
} 