<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ReportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $period;
    public $downloadUrl;

    public function __construct($period, $downloadUrl)
    {
        $this->period = $period;
        $this->downloadUrl = $downloadUrl;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Scheduled Report is Ready')
            ->greeting('Hello Admin,')
            ->line("Your scheduled report for {$this->period} is ready.")
            ->action('Download Report', $this->downloadUrl)
            ->line('Thank you for using SWSS!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Scheduled Report Ready',
            'body' => "Your scheduled report for {$this->period} is ready.",
            'url' => $this->downloadUrl,
        ];
    }
} 