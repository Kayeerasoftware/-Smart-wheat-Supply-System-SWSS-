<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class ChatNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sender;
    protected $message;
    protected $chatType;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $sender, $message, $chatType = 'direct')
    {
        $this->sender = $sender;
        $this->message = $message;
        $this->chatType = $chatType;
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
            'type' => 'chat_message',
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name ?? $this->sender->username,
            'sender_role' => $this->sender->role,
            'message' => $this->message,
            'chat_type' => $this->chatType,
            'title' => $this->getTitle(),
            'message_preview' => $this->getMessagePreview(),
            'icon' => 'fas fa-comments',
            'color' => 'green'
        ];
    }

    private function getTitle()
    {
        $senderName = $this->sender->name ?? $this->sender->username;
        
        switch ($this->chatType) {
            case 'admin':
                return "Message from Admin";
            case 'direct':
                return "New message from {$senderName}";
            case 'group':
                return "New group message";
            default:
                return "New message";
        }
    }

    private function getMessagePreview()
    {
        $preview = substr($this->message, 0, 50);
        return strlen($this->message) > 50 ? $preview . '...' : $preview;
    }
}
