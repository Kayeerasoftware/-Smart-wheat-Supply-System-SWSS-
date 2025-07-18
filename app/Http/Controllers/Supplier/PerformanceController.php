<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();
        $performanceScore = $vendor ? $vendor->total_score : null;
        $scoreFinancial = $vendor ? $vendor->score_financial : null;
        $scoreReputation = $vendor ? $vendor->score_reputation : null;
        $scoreCompliance = $vendor ? $vendor->score_compliance : null;

        // Quality Metrics Calculation
        // Product Quality: % of delivered orders with no returns
        $totalDelivered = \App\Models\Order::where('customer_id', $user->id)->where('status', 'delivered')->count();
        $totalReturned = \App\Models\Order::where('customer_id', $user->id)->where('status', 'returned')->count();
        $productQuality = $totalDelivered > 0 ? round((($totalDelivered - $totalReturned) / $totalDelivered) * 100, 1) : null;

        // Delivery Accuracy: % of orders delivered on or before expected_delivery_date
        $onTimeDeliveries = \App\Models\Order::where('customer_id', $user->id)
            ->where('status', 'delivered')
            ->whereColumn('actual_delivery_date', '<=', 'expected_delivery_date')
            ->count();
        $deliveryAccuracy = $totalDelivered > 0 ? round(($onTimeDeliveries / $totalDelivered) * 100, 1) : null;

        // Response Time: Placeholder (could be calculated from support tickets/messages)
        $responseTime = 92.1; // Placeholder percentage

        $qualityMetrics = [
            'product_quality' => $productQuality,
            'delivery_accuracy' => $deliveryAccuracy,
            'response_time' => $responseTime,
        ];

        // Recent Achievements: 5 most recent delivered orders
        $recentAchievements = \App\Models\Order::where('customer_id', $user->id)
            ->where('status', 'delivered')
            ->orderByDesc('actual_delivery_date')
            ->with(['orderItems.product'])
            ->take(5)
            ->get()
            ->map(function($order) {
                $products = $order->orderItems->map(function($item) {
                    return $item->product ? $item->product->name . ' (x' . $item->quantity . ')' : null;
                })->filter()->implode(', ');
                return [
                    'products' => $products,
                    'delivered_date' => $order->actual_delivery_date ? $order->actual_delivery_date->format('M d, Y') : ($order->updated_at ? $order->updated_at->format('M d, Y') : null),
                    'order_id' => $order->id,
                ];
            });

        return view('performance.index', compact('performanceScore', 'scoreFinancial', 'scoreReputation', 'scoreCompliance', 'qualityMetrics', 'recentAchievements'));
    }
} 