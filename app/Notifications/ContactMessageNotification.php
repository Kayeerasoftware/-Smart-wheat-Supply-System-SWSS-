<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ContactMessageNotification extends Notification
{
    use Queueable;

    public $name;
    public $email;
    public $messageText;

    public function __construct($name, $email, $messageText)
    {
        $this->name = $name;
        $this->email = $email;
        $this->messageText = $messageText;
    }

    public function via($notifiable)
    {
        return ['database']; // Only database for notification bell
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Contact Message',
            'body' => 'From: ' . $this->name . ' (' . $this->email . ')<br>Message: ' . $this->messageText,
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->messageText,
        ];
    }
}
