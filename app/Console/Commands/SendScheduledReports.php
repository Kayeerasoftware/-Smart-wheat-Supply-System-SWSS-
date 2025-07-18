<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReportDeliverySetting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Notifications\ReportReadyNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class SendScheduledReports extends Command
{
    protected $signature = 'reports:send-scheduled';
    protected $description = 'Send scheduled reports to admins based on their delivery settings';

    public function handle()
    {
        $now = Carbon::now();
        $settings = ReportDeliverySetting::with('user')->get();
        Log::info('SendScheduledReports: Found settings', ['count' => $settings->count()]);
        foreach ($settings as $setting) {
            $user = $setting->user;
            Log::info('SendScheduledReports: Processing setting', [
                'user_id' => $user ? $user->id : null,
                'user_email' => $user ? $user->email : null,
                'user_role' => $user ? $user->role : null,
                'frequency' => $setting->frequency,
                'method' => $setting->method,
            ]);
            if (!$user || $user->role !== 'admin') {
                Log::info('SendScheduledReports: Skipping setting (no user or not admin)', ['user_id' => $user ? $user->id : null]);
                continue;
            }

            // Determine if today matches the frequency
            $send = false;
            switch ($setting->frequency) {
                case '5min':
                    $send = true;
                    break;
                case 'daily':
                    $send = true;
                    break;
                case 'weekly':
                    $send = $now->isMonday();
                    break;
                case 'monthly':
                    $send = $now->isSameDay($now->copy()->startOfMonth());
                    break;
            }
            Log::info('SendScheduledReports: Should send?', ['send' => $send]);
            if ($send) {
                // Generate a simple CSV report for the selected period
                $period = $setting->frequency;
                $days = $period === 'daily' ? 1 : ($period === 'weekly' ? 7 : ($period === 'monthly' ? 30 : 1));
                $from = $now->copy()->subDays($days - 1)->startOfDay();
                $to = $now->endOfDay();
                $filename = 'report_' . $user->id . '_' . $period . '_' . $now->format('Ymd_His') . '.csv';
                $path = 'reports/' . $filename;
                $csv = "Date,Activity,Description\n";
                $activities = \App\Models\Activity::where('created_at', '>=', $from)->where('created_at', '<=', $to)->orderBy('created_at', 'desc')->get();
                foreach ($activities as $activity) {
                    $csv .= $activity->created_at->format('Y-m-d H:i') . ',"' . ($activity->type ?? '-') . '","' . str_replace('"', '""', $activity->description ?? '-') . "\n";
                }
                Storage::put($path, $csv);
                $downloadUrl = url('/storage/' . $path);
                // Send notification/email
                $user->notify(new ReportReadyNotification(ucfirst($period), $downloadUrl));
                Log::info("SendScheduledReports: Sent {$period} report to admin {$user->email} via {$setting->method}");
            }
        }
        $this->info('Checked all admin report delivery settings.');
    }
} 