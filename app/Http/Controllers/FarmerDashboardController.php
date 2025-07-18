<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\ManufacturingOrder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Setting;
use App\Services\JavaServerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FarmerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get farmer's wheat inventory
        $wheatInventory = Inventory::where('user_id', $user->id)
            ->whereHas('product', function($query) {
                $query->where('name', 'like', '%wheat%');
            })
            ->with('product')
            ->get();

        // Get recent activity for this user
        $recentActivity = \DB::table('activities')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent supplier purchases (downstream demand)
        $recentSupplierPurchases = PurchaseOrder::where('status', 'completed')
            ->whereHas('items.product', function($query) {
                $query->where('name', 'like', '%wheat%');
            })
            ->with(['items.product', 'supplier'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get supplier demand trends
        $supplierDemandTrends = PurchaseOrderItem::whereHas('purchaseOrder', function($query) {
                $query->where('status', 'completed');
            })
            ->whereHas('product', function($query) {
                $query->where('name', 'like', '%wheat%');
            })
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(quantity_ordered) as total_quantity'),
                DB::raw('COUNT(DISTINCT purchase_order_id) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Load demand forecast data
        $forecastData = $this->loadFarmerDemandForecast();
        
        // Get quality metrics for wheat
        $qualityMetrics = $this->getWheatQualityMetrics($user->id);
        
        // Get market prices (simulated)
        $marketPrices = $this->getWheatMarketPrices();
        
        // Get seasonal trends
        $seasonalTrends = $this->getSeasonalTrends();

        return view('dashboards.farmer', compact(
            'wheatInventory',
            'recentSupplierPurchases',
            'supplierDemandTrends',
            'forecastData',
            'qualityMetrics',
            'marketPrices',
            'seasonalTrends',
            'recentActivity'
        ));
    }

    public function analytics()
    {
        // TODO: Implement real analytics logic and pass data to the view
        return view('analytics.farmer');
    }

    private function loadFarmerDemandForecast()
    {
        $forecastFile = storage_path('app/demand_forecasts/farmer_forecast.json');
        
        if (file_exists($forecastFile)) {
            $forecastData = json_decode(file_get_contents($forecastFile), true);
            
            // Add recommendations based on forecast
            $forecastData['recommendations'] = $this->generateFarmerRecommendations($forecastData);
            
            return $forecastData;
        }
        
        return null;
    }

    private function generateFarmerRecommendations($forecastData)
    {
        $recommendations = [];
        
        if (isset($forecastData['forecast'])) {
            $nextMonth = $forecastData['forecast'][0] ?? 0;
            $trend = $forecastData['trend'] ?? 'stable';
            
            if ($trend === 'increasing') {
                $recommendations[] = [
                    'type' => 'production',
                    'title' => 'Increase Wheat Production',
                    'description' => 'Demand is trending upward. Consider expanding wheat cultivation for the next season.',
                    'priority' => 'high'
                ];
            } elseif ($trend === 'decreasing') {
                $recommendations[] = [
                    'type' => 'storage',
                    'title' => 'Optimize Storage Strategy',
                    'description' => 'Demand is decreasing. Focus on quality storage to maintain wheat value.',
                    'priority' => 'medium'
                ];
            }
            
            if ($nextMonth > 1000) {
                $recommendations[] = [
                    'type' => 'capacity',
                    'title' => 'Scale Up Production Capacity',
                    'description' => 'High demand expected next month. Consider increasing production capacity.',
                    'priority' => 'high'
                ];
            }
        }
        
        return $recommendations;
    }

    private function getWheatQualityMetrics($userId)
    {
        // Simulated quality metrics for wheat
        return [
            'moisture_content' => [
                'current' => 12.5,
                'optimal' => '10-14%',
                'status' => 'optimal'
            ],
            'protein_content' => [
                'current' => 13.2,
                'optimal' => '12-15%',
                'status' => 'optimal'
            ],
            'grade' => [
                'current' => 'Grade A',
                'optimal' => 'Grade A or B',
                'status' => 'excellent'
            ],
            'test_weight' => [
                'current' => 58.5,
                'optimal' => '55-60 lb/bu',
                'status' => 'optimal'
            ]
        ];
    }

    private function getWheatMarketPrices()
    {
        // Simulated market prices
        return [
            'current_price' => 8.75,
            'price_trend' => 'increasing',
            'price_change' => 0.25,
            'price_history' => [
                '1_week_ago' => 8.50,
                '2_weeks_ago' => 8.25,
                '1_month_ago' => 8.00
            ]
        ];
    }

    private function getSeasonalTrends()
    {
        $currentMonth = Carbon::now()->month;
        
        return [
            'current_season' => $this->getSeason($currentMonth),
            'planting_season' => 'September - November',
            'harvest_season' => 'May - July',
            'optimal_planting_time' => $currentMonth >= 9 && $currentMonth <= 11,
            'optimal_harvest_time' => $currentMonth >= 5 && $currentMonth <= 7
        ];
    }

    private function getSeason($month)
    {
        if ($month >= 3 && $month <= 5) return 'Spring';
        if ($month >= 6 && $month <= 8) return 'Summer';
        if ($month >= 9 && $month <= 11) return 'Fall';
        return 'Winter';
    }
} 