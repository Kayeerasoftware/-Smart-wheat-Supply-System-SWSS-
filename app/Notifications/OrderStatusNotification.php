<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusNotification extends Notification
{
    use Queueable;

    public $order;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $status)
    {
        $this->order = $order;
        $this->status = $status;
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
        $statusText = ucfirst($this->status);
        $orderNumber = $this->order->order_number ?? '#' . $this->order->id;
        
        return (new MailMessage)
            ->subject("Order Status Update: {$statusText}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your order {$orderNumber} status has been updated to: {$statusText}")
            ->line($this->getStatusDescription())
            ->action('View Order', url("/orders/{$this->order->id}"))
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
            'type' => 'order_status',
            'title' => 'Order Status Updated',
            'message' => $this->getStatusDescription(),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number ?? '#' . $this->order->id,
            'status' => $this->status,
            'icon' => $this->getStatusIcon(),
            'color' => $this->getStatusColor(),
        ];
    }

    private function getStatusDescription()
    {
        $orderNumber = $this->order->order_number ?? '#' . $this->order->id;
        
        switch ($this->status) {
            case 'pending':
                return "Order {$orderNumber} is pending confirmation. Please review and confirm.";
            case 'confirmed':
                return "Order {$orderNumber} has been confirmed and is being processed.";
            case 'processing':
                return "Order {$orderNumber} is currently being processed and prepared for shipment.";
            case 'shipped':
                return "Order {$orderNumber} has been shipped and is on its way.";
            case 'delivered':
                return "Order {$orderNumber} has been successfully delivered.";
            case 'cancelled':
                return "Order {$orderNumber} has been cancelled.";
            default:
                return "Order {$orderNumber} status has been updated to: " . ucfirst($this->status);
        }
    }

    private function getStatusIcon()
    {
        switch ($this->status) {
            case 'pending':
                return 'fas fa-clock';
            case 'confirmed':
                return 'fas fa-check-circle';
            case 'processing':
                return 'fas fa-cogs';
            case 'shipped':
                return 'fas fa-truck';
            case 'delivered':
                return 'fas fa-check-double';
            case 'cancelled':
                return 'fas fa-times-circle';
            default:
                return 'fas fa-shopping-cart';
        }
    }

    private function getStatusColor()
    {
        switch ($this->status) {
            case 'pending':
                return 'yellow';
            case 'confirmed':
                return 'blue';
            case 'processing':
                return 'purple';
            case 'shipped':
                return 'green';
            case 'delivered':
                return 'green';
            case 'cancelled':
                return 'red';
            default:
                return 'gray';
        }
    }
}
