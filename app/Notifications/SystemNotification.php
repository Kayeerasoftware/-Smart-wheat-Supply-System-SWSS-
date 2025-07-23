<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $type = 'info')
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting("Hello {$notifiable->name}!")
            ->line($this->message)
            ->action('View Details', url('/dashboard'))
            ->line('Thank you for using SWSS!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'system',
            'title' => $this->title,
            'message' => $this->message,
            'notification_type' => $this->type,
            'icon' => $this->getTypeIcon(),
            'color' => $this->getTypeColor(),
        ];
    }

    private function getTypeIcon()
    {
        switch ($this->type) {
            case 'success':
                return 'fas fa-check-circle';
            case 'warning':
                return 'fas fa-exclamation-triangle';
            case 'error':
                return 'fas fa-times-circle';
            case 'info':
            default:
                return 'fas fa-info-circle';
        }
    }

    private function getTypeColor()
    {
        switch ($this->type) {
            case 'success':
                return 'green';
            case 'warning':
                return 'yellow';
            case 'error':
                return 'red';
            case 'info':
            default:
                return 'blue';
        }
    }
}
