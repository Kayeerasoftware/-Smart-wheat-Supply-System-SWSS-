<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportReadyNotification extends Notification
{
    use Queueable;

    public $reportType;
    public $reportData;

    public function __construct($reportType, $reportData = [])
    {
        $this->reportType = $reportType;
        $this->reportData = $reportData;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $title = $this->getReportTitle();
        
        return (new MailMessage)
            ->subject("Report Ready: {$title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your {$title} is ready for review.")
            ->line($this->getReportDescription())
            ->action('View Report', url('/reports'))
            ->line('Thank you for using SWSS!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'report_ready',
            'title' => $this->getReportTitle(),
            'message' => $this->getReportDescription(),
            'report_type' => $this->reportType,
            'report_data' => $this->reportData,
            'icon' => 'fas fa-chart-bar',
            'color' => 'blue',
        ];
    }

    private function getReportTitle()
    {
        switch ($this->reportType) {
            case 'daily_summary':
                return 'Daily Summary Report';
            case 'inventory_report':
                return 'Inventory Report';
            case 'order_report':
                return 'Order Report';
            case 'performance_report':
                return 'Performance Report';
            case 'financial_report':
                return 'Financial Report';
            default:
                return ucfirst(str_replace('_', ' ', $this->reportType)) . ' Report';
        }
    }

    private function getReportDescription()
    {
        switch ($this->reportType) {
            case 'daily_summary':
                $orders = $this->reportData['total_orders'] ?? 0;
                $inventory = $this->reportData['total_inventory'] ?? 0;
                return "Your daily summary shows {$orders} orders and {$inventory} inventory items.";
            case 'inventory_report':
                return 'Your inventory levels have been analyzed and the report is ready for review.';
            case 'order_report':
                return 'Your order processing report is complete and available for download.';
            case 'performance_report':
                return 'Your performance metrics have been calculated and the report is ready.';
            case 'financial_report':
                return 'Your financial summary report has been generated and is ready for review.';
            default:
                return 'Your report has been generated and is ready for review.';
        }
    }
} 