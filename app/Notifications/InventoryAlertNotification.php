<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Inventory;

class InventoryAlertNotification extends Notification
{
    use Queueable;

    public $inventory;
    public $alertType;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inventory $inventory, $alertType)
    {
        $this->inventory = $inventory;
        $this->alertType = $alertType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $title = $this->getAlertTitle();
        $productName = $this->inventory->product->name ?? 'Unknown Product';
        
        return (new MailMessage)
            ->subject("Inventory Alert: {$title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line($this->getAlertDescription())
            ->line("Product: {$productName}")
            ->line("Current Stock: {$this->inventory->quantity_available}")
            ->action('View Inventory', url("/inventory/{$this->inventory->id}"))
            ->line('Please take action to resolve this inventory issue.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'inventory_alert',
            'title' => $this->getAlertTitle(),
            'message' => $this->getAlertDescription(),
            'inventory_id' => $this->inventory->id,
            'product_name' => $this->inventory->product->name ?? 'Unknown Product',
            'current_stock' => $this->inventory->quantity_available,
            'alert_type' => $this->alertType,
            'icon' => $this->getAlertIcon(),
            'color' => $this->getAlertColor(),
        ];
    }

    private function getAlertTitle()
    {
        switch ($this->alertType) {
            case 'low_stock':
                return 'Low Stock Alert';
            case 'out_of_stock':
                return 'Out of Stock Alert';
            case 'expiring_soon':
                return 'Expiring Soon Alert';
            case 'overstock':
                return 'Overstock Alert';
            default:
                return 'Inventory Alert';
        }
    }

    private function getAlertDescription()
    {
        $productName = $this->inventory->product->name ?? 'Unknown Product';
        $currentStock = $this->inventory->quantity_available;
        
        switch ($this->alertType) {
            case 'low_stock':
                return "Product '{$productName}' is running low on stock. Current quantity: {$currentStock}";
            case 'out_of_stock':
                return "Product '{$productName}' is out of stock. Please restock immediately.";
            case 'expiring_soon':
                return "Product '{$productName}' is expiring soon. Current quantity: {$currentStock}";
            case 'overstock':
                return "Product '{$productName}' has excess inventory. Current quantity: {$currentStock}";
            default:
                return "Inventory alert for product '{$productName}'. Current quantity: {$currentStock}";
        }
    }

    private function getAlertIcon()
    {
        switch ($this->alertType) {
            case 'low_stock':
                return 'fas fa-exclamation-triangle';
            case 'out_of_stock':
                return 'fas fa-times-circle';
            case 'expiring_soon':
                return 'fas fa-clock';
            case 'overstock':
                return 'fas fa-arrow-up';
            default:
                return 'fas fa-boxes';
        }
    }

    private function getAlertColor()
    {
        switch ($this->alertType) {
            case 'low_stock':
                return 'yellow';
            case 'out_of_stock':
                return 'red';
            case 'expiring_soon':
                return 'orange';
            case 'overstock':
                return 'blue';
            default:
                return 'gray';
        }
    }
}
