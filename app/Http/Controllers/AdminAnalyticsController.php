<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function customerSegments()
    {
        $segmentsPath = storage_path('app/public/segments/customer_segments.json');
        $segments = [];
        
        if (file_exists($segmentsPath)) {
            $segments = json_decode(file_get_contents($segmentsPath), true);
        }
        
        return view('admin.analytics.customer-segments', compact('segments'));
    }

    public function runSegmentation()
    {
        // Run the Python segmentation script
        $scriptPath = base_path('ml_scripts/customer_segmentation.py');
        $output = shell_exec("python \"$scriptPath\" 2>&1");
        
        return redirect()->back()->with('success', 'Customer segmentation completed! ' . $output);
    }

    public function runDiscountCalculation()
    {
        // Run the Python discount calculation script
        $scriptPath = base_path('ml_scripts/discount_calculator.py');
        $output = shell_exec("python \"$scriptPath\" 2>&1");
        
        return redirect()->back()->with('success', 'Discount calculations completed! ' . $output);
    }

    public function showDiscountAnalytics()
    {
        $discountsPath = storage_path('app/public/discounts/discount_recommendations.json');
        $discounts = [];
        
        if (file_exists($discountsPath)) {
            $discounts = json_decode(file_get_contents($discountsPath), true);
        }
        
        return view('admin.analytics.discounts', compact('discounts'));
    }
} 