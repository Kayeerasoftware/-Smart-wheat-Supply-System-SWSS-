<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogisticsRoute;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\Shipment;
use App\Models\Activity;
use Carbon\Carbon;

class DistributorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent activities
        $recentActivities = Activity::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();
        
        // Get active routes
        $activeRoutes = LogisticsRoute::where('status', '!=', 'Delivered')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get today's deliveries
        $todayDeliveries = LogisticsRoute::whereDate('created_at', Carbon::today())
            ->get();
        
        // Get pending shipments
        $pendingShipments = Shipment::where('status', 'pending')
            ->orWhere('status', 'in_transit')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('dashboards.distributor', compact(
            'stats', 
            'recentActivities', 
            'activeRoutes', 
            'todayDeliveries',
            'pendingShipments'
        ));
    }

    public function routes(Request $request)
    {
        $query = LogisticsRoute::query();
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('origin', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%")
                  ->orWhere('deliverer', 'like', "%{$search}%")
                  ->orWhere('route_id', 'like', "%{$search}%");
            });
        }
        
        $routes = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('distributor.routes', compact('routes'));
    }

    public function inventory(Request $request)
    {
        $query = Inventory::with(['product', 'warehouse']);
        
        // Apply filters
        if ($request->filled('warehouse')) {
            $query->where('warehouse_id', $request->warehouse);
        }
        
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'low') {
                $query->where('quantity_available', '<=', 10)->where('quantity_available', '>', 0);
            } elseif ($status === 'out') {
                $query->where('quantity_available', '<=', 0);
            } elseif ($status === 'in_stock') {
                $query->where('quantity_available', '>', 10);
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        
        $inventory = $query->paginate(15);
        $warehouses = \App\Models\Warehouse::all();
        
        return view('distributor.inventory', compact('inventory', 'warehouses'));
    }

    public function shipments(Request $request)
    {
        $query = Shipment::with(['order']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $shipments = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('distributor.shipments', compact('shipments'));
    }

    public function analytics(Request $request)
    {
        $period = $request->get('period', '30'); // Default 30 days
        $startDate = Carbon::now()->subDays($period);
        
        // Delivery performance metrics
        $deliveryMetrics = $this->getDeliveryMetrics($startDate);
        
        // Route efficiency metrics
        $routeMetrics = $this->getRouteMetrics($startDate);
        
        // Inventory turnover metrics
        $inventoryMetrics = $this->getInventoryMetrics($startDate);
        
        return view('distributor.analytics', compact(
            'deliveryMetrics',
            'routeMetrics', 
            'inventoryMetrics',
            'period'
        ));
    }

    private function getDashboardStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'active_routes' => LogisticsRoute::where('status', '!=', 'Delivered')->count(),
            'today_deliveries' => [
                'scheduled' => LogisticsRoute::whereDate('created_at', $today)->count(),
                'completed' => LogisticsRoute::whereDate('created_at', $today)
                    ->where('status', 'Delivered')->count(),
            ],
            'pending_shipments' => Shipment::whereIn('status', ['pending', 'in_transit'])->count(),
            'total_inventory_value' => Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                ->sum(\DB::raw('inventories.quantity_on_hand * products.cost_price')),
            'low_stock_items' => Inventory::where('quantity_available', '<=', 10)
                ->where('quantity_available', '>', 0)->count(),
            'out_of_stock_items' => Inventory::where('quantity_available', '<=', 0)->count(),
            'weekly_deliveries' => LogisticsRoute::where('created_at', '>=', $thisWeek)
                ->where('status', 'Delivered')->count(),
            'monthly_revenue' => Order::where('created_at', '>=', $thisMonth)
                ->where('status', 'completed')->sum('total_amount'),
            'avg_delivery_time' => '2.3 hours', // This would be calculated from actual data
            'on_time_delivery_rate' => 94.5, // This would be calculated from actual data
        ];
    }

    private function getDeliveryMetrics($startDate)
    {
        return [
            'total_deliveries' => LogisticsRoute::where('created_at', '>=', $startDate)->count(),
            'completed_deliveries' => LogisticsRoute::where('created_at', '>=', $startDate)
                ->where('status', 'Delivered')->count(),
            'on_time_deliveries' => LogisticsRoute::where('created_at', '>=', $startDate)
                ->where('status', 'Delivered')
                // Add logic for on-time calculation
                ->count(),
            'average_delivery_time' => 2.3, // Calculate from actual data
            'delivery_success_rate' => 96.8,
        ];
    }

    private function getRouteMetrics($startDate)
    {
        return [
            'total_routes' => LogisticsRoute::where('created_at', '>=', $startDate)->count(),
            'active_routes' => LogisticsRoute::where('created_at', '>=', $startDate)
                ->where('status', '!=', 'Delivered')->count(),
            'route_efficiency' => 89.2, // Calculate based on actual metrics
            'fuel_efficiency' => 12.5, // km per liter
            'distance_covered' => LogisticsRoute::where('created_at', '>=', $startDate)->count() * 25, // Estimate
        ];
    }

    private function getInventoryMetrics($startDate)
    {
        return [
            'inventory_turnover' => 4.2, // Calculate from actual data
            'stock_accuracy' => 98.5,
            'carrying_cost' => 15000, // Calculate from actual data
            'stockout_incidents' => Inventory::where('updated_at', '>=', $startDate)
                ->where('quantity_available', '<=', 0)->count(),
        ];
    }

    public function updateRouteStatus(Request $request, $routeId)
    {
        $request->validate([
            'status' => 'required|in:In Transit,Delayed,Delivered'
        ]);

        $route = LogisticsRoute::findOrFail($routeId);
        $route->update(['status' => $request->status]);

        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'Route status updated',
            'description' => "Route {$route->route_id} status changed to {$request->status}",
            'created_at' => now(),
        ]);

        return response()->json(['success' => true, 'route' => $route]);
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:delivery,inventory,route_efficiency',
            'period' => 'required|in:7,30,90',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        // Generate report based on type and period
        $reportData = $this->getReportData($request->type, $request->period);
        
        // In a real implementation, you would generate the actual file
        // For now, we'll just return a success response
        
        return response()->json([
            'success' => true,
            'message' => 'Report generated successfully',
            'download_url' => '/reports/download/' . uniqid()
        ]);
    }

    private function getReportData($type, $period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        switch ($type) {
            case 'delivery':
                return LogisticsRoute::where('created_at', '>=', $startDate)->get();
            case 'inventory':
                return Inventory::with(['product', 'warehouse'])->get();
            case 'route_efficiency':
                return LogisticsRoute::where('created_at', '>=', $startDate)
                    ->selectRaw('origin, destination, COUNT(*) as frequency, AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_time')
                    ->groupBy('origin', 'destination')
                    ->get();
            default:
                return collect();
        }
    }
}
