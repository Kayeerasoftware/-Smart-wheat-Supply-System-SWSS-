<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;

class RetailerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:retailer');
    }

    public function index()
    {
        $user = auth()->user();
        $role = $user->role;
        $userId = $user->id;
        
        // Load demand forecast if available
        $forecast = [];
        $forecastPath = storage_path("app/public/forecasts/forecast_{$role}_{$userId}.json");
        if (file_exists($forecastPath)) {
            $forecast = json_decode(file_get_contents($forecastPath), true);
        }
        
        // Load customer segments if available
        $customerSegments = [];
        $segmentsPath = storage_path('app/public/segments/customer_segments.json');
        if (file_exists($segmentsPath)) {
            $customerSegments = json_decode(file_get_contents($segmentsPath), true);
        }
        
        // Get recent activity for the user
        $recentActivity = Activity::where('user_id', $user->user_id)
            ->latest()
            ->take(5)
            ->get();

        // Get retailer's sales data
        $todaySales = $this->getTodaySales($user);
        $activeOrders = $this->getActiveOrders($user);
        $lowStockItems = $this->getLowStockItems($user);
        $totalInventory = $this->getTotalInventory($user);
        
        // Get customer insights
        $customerInsights = $this->getCustomerInsights($user, $customerSegments);
        
        // Get top products
        $topProducts = $this->getTopProducts($user);
        
        // Get sales trends
        $salesTrends = $this->getSalesTrends($user);

        $data = [
            'recentActivity' => $recentActivity,
            'todaySales' => $todaySales,
            'activeOrders' => $activeOrders,
            'lowStockItems' => $lowStockItems,
            'totalInventory' => $totalInventory,
            'customerInsights' => $customerInsights,
            'topProducts' => $topProducts,
            'salesTrends' => $salesTrends,
            'forecast' => $forecast,
            'customerSegments' => $customerSegments,
        ];

        return view('dashboards.retailer', $data);
    }

    private function getTodaySales($user)
    {
        return Order::where('customer_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('total_amount') ?? 2450.00;
    }

    private function getActiveOrders($user)
    {
        return Order::where('customer_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->count() ?? 8;
    }

    private function getLowStockItems($user)
    {
        return Inventory::where('quantity_available', '<=', 10)
            ->where('quantity_available', '>', 0)
            ->whereHas('product', function($query) {
                $query->where('type', 'processed');
            })
            ->count() ?? 5;
    }

    private function getTotalInventory($user)
    {
        return Inventory::whereHas('product', function($query) {
            $query->where('type', 'processed');
        })->sum('quantity_available') ?? 1250;
    }

    private function getCustomerInsights($user, $customerSegments)
    {
        $insights = [
            'newCustomers' => 12,
            'topProduct' => 'Whole Wheat Flour',
            'satisfaction' => 4.8,
            'satisfactionChange' => 0.2,
        ];

        // If customer segments exist, enhance insights
        if (!empty($customerSegments)) {
            $customers = $customerSegments['customers'] ?? [];
            $insights['totalCustomers'] = count($customers);
            $insights['segmentBreakdown'] = $this->getSegmentBreakdown($customers);
            $insights['recommendations'] = $this->getCustomerRecommendations($customers);
        }

        return $insights;
    }

    private function getSegmentBreakdown($customers)
    {
        $breakdown = [];
        foreach ($customers as $customer) {
            $segment = $customer['segment_name'] ?? 'Unknown';
            $breakdown[$segment] = ($breakdown[$segment] ?? 0) + 1;
        }
        return $breakdown;
    }

    private function getCustomerRecommendations($customers)
    {
        $recommendations = [];
        
        // Analyze segments and provide recommendations
        $segmentCounts = [];
        foreach ($customers as $customer) {
            $segment = $customer['segment_name'] ?? 'Unknown';
            $segmentCounts[$segment] = ($segmentCounts[$segment] ?? 0) + 1;
        }

        if (isset($segmentCounts['Champions'])) {
            $recommendations[] = "Focus on Champions (high-value customers) with exclusive offers";
        }
        if (isset($segmentCounts['Loyal Customers'])) {
            $recommendations[] = "Reward Loyal Customers with loyalty programs";
        }
        if (isset($segmentCounts['Recent Customers'])) {
            $recommendations[] = "Engage Recent Customers with welcome offers";
        }
        if (isset($segmentCounts['Frequent Customers'])) {
            $recommendations[] = "Offer volume discounts to Frequent Customers";
        }
        if (isset($segmentCounts['Big Spenders'])) {
            $recommendations[] = "Provide premium service to Big Spenders";
        }

        return $recommendations;
    }

    private function getTopProducts($user)
    {
        return [
            ['name' => 'Whole Wheat Flour', 'sales' => 1250, 'growth' => 12],
            ['name' => 'Bread Mix', 'sales' => 890, 'growth' => 8],
            ['name' => 'Pasta', 'sales' => 650, 'growth' => 15],
            ['name' => 'Cereal', 'sales' => 420, 'growth' => 5],
        ];
    }

    private function getSalesTrends($user)
    {
        $trends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $trends[] = [
                'date' => $date->format('M d'),
                'sales' => rand(2000, 3000),
                'orders' => rand(30, 60),
            ];
        }
        return $trends;
    }

    public function customerSegments()
    {
        $user = auth()->user();
        
        // Load customer segments
        $segments = [];
        $segmentsPath = storage_path('app/public/segments/customer_segments.json');
        if (file_exists($segmentsPath)) {
            $segments = json_decode(file_get_contents($segmentsPath), true);
        }

        return view('retailer.customer-segments', compact('segments'));
    }

    public function runSegmentation()
    {
        $scriptPath = base_path('ml_scripts/customer_segmentation.py');
        $output = shell_exec("python \"$scriptPath\" 2>&1");
        
        return redirect()->back()->with('success', 'Customer segmentation completed! ' . $output);
    }

    public function personalizedRecommendations()
    {
        $user = auth()->user();
        
        // Load customer segments and discounts
        $segments = [];
        $segmentsPath = storage_path('app/public/segments/customer_segments.json');
        if (file_exists($segmentsPath)) {
            $segments = json_decode(file_get_contents($segmentsPath), true);
        }

        $discounts = [];
        $discountsPath = storage_path('app/public/discounts/retailer_discounts.json');
        if (file_exists($discountsPath)) {
            $discounts = json_decode(file_get_contents($discountsPath), true);
        }

        return view('retailer.recommendations', compact('segments', 'discounts'));
    }
} 