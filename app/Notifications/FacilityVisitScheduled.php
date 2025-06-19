<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\FacilityVisit;

class FacilityVisitScheduled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $visit;

    public function __construct(FacilityVisit $visit)
    {
        $this->visit = $visit;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Facility Visit Scheduled')
            ->greeting('Hello ' . $notifiable->username . ',')
            ->line('A facility visit has been scheduled for your vendor application.')
            ->line('Date: ' . $this->visit->scheduled_at->format('M d, Y'))
            ->line('Notes: ' . ($this->visit->notes ?? 'No notes'))
            ->action('View Dashboard', url('/supplier/dashboard'))
            ->line('Please be prepared for the visit. If you have questions, reply to this email.');
    }
} 