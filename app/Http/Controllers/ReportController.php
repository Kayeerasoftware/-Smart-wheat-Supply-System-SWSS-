<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Mail\DeliveryReportMail;
use Illuminate\Support\Facades\Mail;

class ReportController extends Controller
{
    public function generateDeliveryReport()
    {
        // Example data for the report (replace with real data as needed)
        $deliveries = [
            ['id' => 1, 'client' => 'Client A', 'status' => 'Delivered', 'date' => '2024-06-20'],
            ['id' => 2, 'client' => 'Client B', 'status' => 'In Transit', 'date' => '2024-06-20'],
            ['id' => 3, 'client' => 'Client C', 'status' => 'Scheduled', 'date' => '2024-06-21'],
            ['id' => 4, 'client' => 'Client D', 'status' => 'Delivered', 'date' => '2024-06-19'],
        ];

        // Generate PDF
        $pdf = PDF::loadView('reports.delivery_report_pdf', compact('deliveries'));
        
        // Return the PDF for download/view in browser
        return $pdf->stream('delivery_report.pdf');
    }

    public function showReportPage()
    {
        return view('reports.generate');
    }

    public function sendEmailReport(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;

        // Example data for the report
        $deliveries = [
            ['id' => 1, 'client' => 'Client A', 'status' => 'Delivered', 'date' => '2024-06-20'],
            ['id' => 2, 'client' => 'Client B', 'status' => 'In Transit', 'date' => '2024-06-20'],
            ['id' => 3, 'client' => 'Client C', 'status' => 'Scheduled', 'date' => '2024-06-21'],
            ['id' => 4, 'client' => 'Client D', 'status' => 'Delivered', 'date' => '2024-06-19'],
        ];

        // Generate PDF
        $pdf = PDF::loadView('reports.delivery_report_pdf', compact('deliveries'));
        $pdfContent = $pdf->output();

        // Send email
        Mail::to($email)->send(new DeliveryReportMail($pdfContent));

        return redirect()->back()->with('success', 'Report sent to ' . $email);
    }
} 