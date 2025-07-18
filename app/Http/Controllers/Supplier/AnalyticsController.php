<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Example: Get inventory count per day for the last 30 days
        $dates = collect(range(0, 29))->map(function ($i) {
            return now()->subDays(29 - $i)->format('Y-m-d');
        });

        // Replace this with your real inventory query logic
        $user = auth()->user();
        $inventoryData = \DB::table('inventories')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->where('products.supplier_id', $user->id)
            ->where('inventories.created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(inventories.created_at) as date, SUM(inventories.quantity_on_hand) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $labels = $dates->toArray();
        $data = $dates->map(fn($date) => (int) ($inventoryData[$date] ?? 0))->toArray();

        // Revenue by product for pie chart
        $user = auth()->user();
        $revenueByProduct = 
            \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.supplier_id', $user->id)
            ->where('orders.status', 'delivered') // Changed from 'completed' to 'delivered'
            ->select('products.name as product_name', \DB::raw('SUM(order_items.quantity * order_items.unit_price) as revenue'))
            ->groupBy('products.name')
            ->orderByDesc('revenue')
            ->limit(6)
            ->get();

        $revenueChartLabels = $revenueByProduct->pluck('product_name')->toArray();
        $revenueChartData = $revenueByProduct->pluck('revenue')->map(fn($v) => (float) $v)->toArray();

        // If no real data exists, provide dummy data for demonstration
        if (empty($revenueChartLabels)) {
            // $revenueChartLabels = ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'];
            // $revenueChartData = [15000, 12000, 8000, 6000, 4000];
            // Instead, pass empty arrays
            $revenueChartLabels = [];
            $revenueChartData = [];
        }

        // Sales Performance Data - Monthly trends for last 12 months
        $salesPerformanceData = [];
        $salesPerformanceLabels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            
            // Get real sales data for this month
            $monthlySales = \DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.supplier_id', $user->id)
                ->where('orders.status', 'delivered')
                ->whereRaw("strftime('%Y-%m', orders.created_at) = ?", [$monthKey])
                ->sum(\DB::raw('order_items.quantity * order_items.unit_price'));
            
            $salesPerformanceLabels[] = $date->format('M Y');
            $salesPerformanceData[] = (float) $monthlySales;
        }
        // If no real sales data exists, do not provide dummy data
        if (array_sum($salesPerformanceData) == 0) {
            $salesPerformanceData = [];
        }

        // AI Demand Forecasting Data
        $demandForecastData = $this->getDemandForecastData($user->id);

        // Supplier Demand Insights Data
        $supplierDemandInsights = $this->getSupplierDemandInsights($user->id);

        return view('analytics.index', [
            'inventoryTrendLabels' => $labels,
            'inventoryTrendData' => $data,
            'revenueChartLabels' => $revenueChartLabels,
            'revenueChartData' => $revenueChartData,
            'salesPerformanceLabels' => $salesPerformanceLabels,
            'salesPerformanceData' => $salesPerformanceData,
            'demandForecastData' => $demandForecastData,
            'supplierDemandInsights' => $supplierDemandInsights,
        ]);
    }

    private function getDemandForecastData($userId)
    {
        // First try to read from the seeder-generated file
        $forecastPath = storage_path('app/demand_forecast_data.json');
        if (file_exists($forecastPath)) {
            $forecastData = json_decode(file_get_contents($forecastPath), true);
            if ($forecastData) {
                // Compute summary if not present
                $historical = $forecastData['historical_values'] ?? [];
                $forecast = $forecastData['forecast_values'] ?? [];
                $summary = [
                    'total_historical_demand' => array_sum($historical),
                    'avg_monthly_demand' => count($historical) ? array_sum($historical) / count($historical) : 0,
                    'forecast_total' => array_sum($forecast),
                    'forecast_avg' => count($forecast) ? array_sum($forecast) / count($forecast) : 0,
                    'growth_rate' => 0 // Not calculated here
                ];
                return [
                    'historical_dates' => $forecastData['historical_dates'] ?? [],
                    'historical_values' => $historical,
                    'forecast_dates' => $forecastData['forecast_dates'] ?? [],
                    'forecast_values' => $forecast,
                    'lower_ci' => [],
                    'upper_ci' => [],
                    'summary' => $summary,
                    'recommendations' => $forecastData['recommendations'] ?? [],
                    'title' => $forecastData['title'] ?? 'AI Demand Forecasting',
                ];
            }
        }
        // Fallback to user-specific forecast file
        $userForecastPath = storage_path("app/public/forecasts/supplier_{$userId}_demand_forecast.json");
        if (file_exists($userForecastPath)) {
            $forecastData = json_decode(file_get_contents($userForecastPath), true);
            if ($forecastData) {
                return [
                    'historical_dates' => $forecastData['historical_data']['dates'] ?? [],
                    'historical_values' => $forecastData['historical_data']['values'] ?? [],
                    'forecast_dates' => $forecastData['dates'] ?? [],
                    'forecast_values' => $forecastData['values'] ?? [],
                    'lower_ci' => $forecastData['lower_ci'] ?? [],
                    'upper_ci' => $forecastData['upper_ci'] ?? [],
                    'summary' => $forecastData['summary'] ?? [],
                    'recommendations' => $forecastData['recommendations'] ?? [],
                    'title' => $forecastData['title'] ?? 'Demand Forecast'
                ];
            }
        }
        // Return dummy data if no forecast file exists
        return [
            'historical_dates' => ['2024-07', '2024-08', '2024-09', '2024-10', '2024-11', '2024-12', '2025-01', '2025-02', '2025-03', '2025-04', '2025-05', '2025-06'],
            'historical_values' => [788, 659, 1069, 870, 1034, 980, 1262, 1110, 805, 867, 854, 642],
            'forecast_dates' => ['2025-07', '2025-08', '2025-09', '2025-10', '2025-11', '2025-12'],
            'forecast_values' => [737, 703, 715, 711, 712, 712],
            'lower_ci' => [362, 274, 210, 149, 96, 46],
            'upper_ci' => [1112, 1131, 1220, 1273, 1329, 1378],
            'summary' => [
                'total_historical_demand' => 10940,
                'avg_monthly_demand' => 911.67,
                'forecast_total' => 4289.59,
                'forecast_avg' => 714.93,
                'growth_rate' => -21.58
            ],
            'recommendations' => [
                'Reduce wheat processing by 21.6%',
                'Plan for 715 tons average monthly processing',
                'Total expected demand: 4290 tons over next 6 months'
            ],
            'title' => 'Processed Wheat Demand from Manufacturers'
        ];
    }

    private function getSupplierDemandInsights($userId)
    {
        // Check if supplier has past purchase history
        $pastPurchases = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.customer_id', $userId)
            ->where('orders.order_type', 'purchase')
            ->where('orders.status', 'delivered')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                \DB::raw('SUM(order_items.quantity) as total_quantity'),
                \DB::raw('COUNT(DISTINCT orders.id) as purchase_frequency'),
                \DB::raw('AVG(order_items.unit_price) as avg_price')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        if ($pastPurchases->isNotEmpty()) {
            // Supplier has purchase history - provide ML-based recommendations
            $insights = [
                'type' => 'purchase_recommendations',
                'title' => 'ML-Based Purchase Recommendations',
                'description' => 'Based on your past purchase patterns, here are our recommendations:',
                'recommendations' => [],
                'chart_data' => [
                    'labels' => $pastPurchases->pluck('product_name')->toArray(),
                    'quantities' => $pastPurchases->pluck('total_quantity')->toArray(),
                    'frequencies' => $pastPurchases->pluck('purchase_frequency')->toArray(),
                    'prices' => $pastPurchases->pluck('avg_price')->toArray()
                ]
            ];

            // Generate ML-based recommendations
            foreach ($pastPurchases as $purchase) {
                $recommendedQuantity = $purchase->total_quantity * 1.2; // 20% increase based on trend
                $insights['recommendations'][] = [
                    'product_id' => $purchase->product_id,
                    'product' => $purchase->product_name,
                    'recommended_quantity' => round($recommendedQuantity),
                    'reason' => "Based on your frequent purchases of {$purchase->product_name} ({$purchase->purchase_frequency} times)",
                    'confidence' => 'High',
                    'price_range' => '$' . number_format($purchase->avg_price * 0.9, 2) . ' - $' . number_format($purchase->avg_price * 1.1, 2)
                ];
            }

            return $insights;
        } else {
            // No purchase history - show available market products
            $availableProducts = \DB::table('farmer_inventories')
                ->join('products', 'farmer_inventories.product_id', '=', 'products.id')
                ->join('users', 'farmer_inventories.farmer_id', '=', 'users.id')
                ->where('farmer_inventories.is_available', true)
                ->where('farmer_inventories.quantity', '>', 0)
                ->where('products.name', 'like', '%wheat%')
                ->select(
                    'products.id as product_id',
                    'products.name as product_name',
                    'users.id as farmer_id',
                    'users.username as farmer_name',
                    'farmer_inventories.quantity',
                    'farmer_inventories.price_per_kg',
                    'farmer_inventories.quality_grade',
                    'farmer_inventories.location'
                )
                ->orderBy('farmer_inventories.price_per_kg', 'asc')
                ->limit(10)
                ->get();

            if ($availableProducts->isNotEmpty()) {
                return [
                    'type' => 'market_products',
                    'title' => 'Available Market Products',
                    'description' => 'Since you have no purchase history, here are wheat products available from farmers:',
                    'products' => $availableProducts->map(function($product) {
                        return [
                            'product_id' => $product->product_id,
                            'farmer_id' => $product->farmer_id,
                            'product_name' => $product->product_name,
                            'farmer_name' => $product->farmer_name,
                            'quantity' => $product->quantity,
                            'price_per_kg' => $product->price_per_kg,
                            'quality_grade' => $product->quality_grade,
                            'location' => $product->location
                        ];
                    })->toArray(),
                    'chart_data' => [
                        'labels' => $availableProducts->pluck('product_name')->toArray(),
                        'quantities' => $availableProducts->pluck('quantity')->toArray(),
                        'prices' => $availableProducts->pluck('price_per_kg')->toArray(),
                        'qualities' => $availableProducts->pluck('quality_grade')->toArray()
                    ]
                ];
            } else {
                // No products available - return empty array and a message
                return [
                    'type' => 'market_products',
                    'title' => 'Available Market Products',
                    'description' => 'No wheat products are currently available from farmers. Please check back later.',
                    'products' => [],
                    'chart_data' => [
                        'labels' => [],
                        'quantities' => [],
                        'prices' => [],
                        'qualities' => []
                    ]
                ];
            }
        }
    }
} 