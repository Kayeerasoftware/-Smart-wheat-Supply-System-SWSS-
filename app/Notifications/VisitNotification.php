<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\FacilityVisit;

class VisitNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $visit;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(FacilityVisit $visit, $type = 'scheduled')
    {
        $this->visit = $visit;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'facility_visit',
            'visit_id' => $this->visit->id,
            'vendor_id' => $this->visit->vendor_id,
            'scheduled_at' => $this->visit->scheduled_at,
            'status' => $this->visit->status,
            'notes' => $this->visit->notes,
            'notification_type' => $this->type,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'icon' => 'fas fa-calendar-check',
            'color' => 'blue'
        ];
    }

    private function getTitle()
    {
        switch ($this->type) {
            case 'scheduled':
                return 'Facility Visit Scheduled';
            case 'rescheduled':
                return 'Facility Visit Rescheduled';
            case 'completed':
                return 'Facility Visit Completed';
            case 'cancelled':
                return 'Facility Visit Cancelled';
            default:
                return 'Facility Visit Update';
        }
    }

    private function getMessage()
    {
        $date = $this->visit->scheduled_at->format('M d, Y \a\t g:i A');
        
        switch ($this->type) {
            case 'scheduled':
                return "A facility visit has been scheduled for {$date}. Please be prepared for the inspection.";
            case 'rescheduled':
                return "Your facility visit has been rescheduled to {$date}. Please update your calendar.";
            case 'completed':
                return "Your facility visit has been completed. Check your dashboard for the results.";
            case 'cancelled':
                return "Your facility visit scheduled for {$date} has been cancelled. We'll contact you to reschedule.";
            default:
                return "Your facility visit has been updated. Scheduled for {$date}.";
        }
    }
}
