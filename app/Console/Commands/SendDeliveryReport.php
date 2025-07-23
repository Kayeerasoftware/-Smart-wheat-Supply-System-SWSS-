<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\DeliveryReportMail;
use Illuminate\Support\Facades\Mail;
use PDF;

class SendDeliveryReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:send-delivery {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send a delivery report to the specified email address.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        // Example data for the report (replace with real data as needed)
        $deliveries = [
            ['id' => 1, 'client' => 'Client A', 'status' => 'Delivered', 'date' => '2024-06-20'],
            ['id' => 2, 'client' => 'Client B', 'status' => 'In Transit', 'date' => '2024-06-20'],
        ];

        // Generate PDF from Blade view
        $pdf = \PDF::loadView('reports.delivery_report_pdf', compact('deliveries'));
        $pdfContent = $pdf->output();

        // Send email with PDF attachment
        \Mail::to($email)->send(new \App\Mail\DeliveryReportMail($pdfContent));

        $this->info('Delivery report sent to ' . $email);
    }
}
