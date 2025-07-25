<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\Shipment;
use App\Models\ManufacturingOrder;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminSupplyChainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display supply chain dashboard (admin view)
     */
    public function index(Request $request)
    {
        try {
            // Get summary statistics with error handling
            $summary = [
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_purchase_orders' => PurchaseOrder::count(),
                'pending_purchase_orders' => PurchaseOrder::where('status', 'pending')->count(),
                'total_shipments' => Shipment::count(),
                'in_transit_shipments' => Shipment::where('status', 'in_transit')->count(),
                'total_manufacturing_orders' => ManufacturingOrder::count(),
                'active_manufacturing_orders' => ManufacturingOrder::whereIn('status', ['in_progress', 'scheduled'])->count(),
            ];

            // Get recent orders with error handling
            $recentOrders = collect();
            try {
                $recentOrders = Order::with(['items.product', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                Log::error('Error loading recent orders: ' . $e->getMessage());
            }

            // Get recent purchase orders with error handling
            $recentPurchaseOrders = collect();
            try {
                $recentPurchaseOrders = PurchaseOrder::with(['items.product', 'supplier'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                Log::error('Error loading recent purchase orders: ' . $e->getMessage());
            }

            // Get recent shipments with error handling
            $recentShipments = collect();
            try {
                $recentShipments = Shipment::with(['order', 'warehouse'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                Log::error('Error loading recent shipments: ' . $e->getMessage());
            }

            // Get recent manufacturing orders with error handling
            $recentManufacturingOrders = collect();
            try {
                $recentManufacturingOrders = ManufacturingOrder::with(['product', 'productionLine'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                Log::error('Error loading recent manufacturing orders: ' . $e->getMessage());
            }

            // Get supply chain metrics with error handling
            $metrics = [
                'order_fulfillment_rate' => $this->calculateOrderFulfillmentRate(),
                'average_order_processing_time' => $this->calculateAverageOrderProcessingTime(),
                'supplier_performance_score' => $this->calculateSupplierPerformanceScore(),
                'inventory_turnover_rate' => $this->calculateInventoryTurnoverRate(),
            ];

            return view('admin.supply-chain.index', compact(
                'summary', 
                'recentOrders', 
                'recentPurchaseOrders', 
                'recentShipments', 
                'recentManufacturingOrders',
                'metrics'
            ));

        } catch (\Exception $e) {
            Log::error('Supply chain dashboard error: ' . $e->getMessage());
            
            // Return a simplified view with empty data if there's an error
            $summary = [
                'total_orders' => 0,
                'pending_orders' => 0,
                'total_purchase_orders' => 0,
                'pending_purchase_orders' => 0,
                'total_shipments' => 0,
                'in_transit_shipments' => 0,
                'total_manufacturing_orders' => 0,
                'active_manufacturing_orders' => 0,
            ];

            $metrics = [
                'order_fulfillment_rate' => 0,
                'average_order_processing_time' => 0,
                'supplier_performance_score' => 0,
                'inventory_turnover_rate' => 0,
            ];

            return view('admin.supply-chain.index', compact(
                'summary', 
                'recentOrders', 
                'recentPurchaseOrders', 
                'recentShipments', 
                'recentManufacturingOrders',
                'metrics'
            ));
        }
    }

    /**
     * Display orders management page
     */
    public function orders(Request $request)
    {
        $query = Order::with(['items.product', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        $suppliers = User::where('role', 'supplier')->get();

        return view('admin.supply-chain.orders', compact('orders', 'suppliers'));
    }

    /**
     * Display purchase orders management page
     */
    public function purchaseOrders(Request $request)
    {
        $query = PurchaseOrder::with(['items.product', 'supplier']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')->paginate(20);
        $suppliers = User::where('role', 'supplier')->get();

        return view('admin.supply-chain.purchase-orders', compact('purchaseOrders', 'suppliers'));
    }

    /**
     * Display shipments management page
     */
    public function shipments(Request $request)
    {
        $query = Shipment::with(['order', 'warehouse']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        $shipments = $query->orderBy('created_at', 'desc')->paginate(20);
        $warehouses = Warehouse::active()->get();

        return view('admin.supply-chain.shipments', compact('shipments', 'warehouses'));
    }

    /**
     * Display manufacturing orders management page
     */
    public function manufacturingOrders(Request $request)
    {
        $query = ManufacturingOrder::with(['product', 'productionLine']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $manufacturingOrders = $query->orderBy('created_at', 'desc')->paginate(20);
        $products = Product::active()->get();

        return view('admin.supply-chain.manufacturing-orders', compact('manufacturingOrders', 'products'));
    }

    /**
     * Display supply chain analytics
     */
    public function analytics()
    {
        // Order analytics
        $orderAnalytics = [
            'total_orders' => Order::count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'average_order_value' => Order::where('status', 'completed')->avg('total_amount'),
        ];

        // Purchase order analytics
        $purchaseOrderAnalytics = [
            'total_purchase_orders' => PurchaseOrder::count(),
            'pending_purchase_orders' => PurchaseOrder::where('status', 'pending')->count(),
            'approved_purchase_orders' => PurchaseOrder::where('status', 'approved')->count(),
            'total_purchase_value' => PurchaseOrder::sum('total_amount'),
        ];

        // Shipment analytics
        $shipmentAnalytics = [
            'total_shipments' => Shipment::count(),
            'delivered_shipments' => Shipment::where('status', 'delivered')->count(),
            'in_transit_shipments' => Shipment::where('status', 'in_transit')->count(),
            'average_delivery_time' => $this->calculateAverageDeliveryTime(),
        ];

        // Manufacturing analytics
        $manufacturingAnalytics = [
            'total_manufacturing_orders' => ManufacturingOrder::count(),
            'completed_manufacturing_orders' => ManufacturingOrder::where('status', 'completed')->count(),
            'in_progress_manufacturing_orders' => ManufacturingOrder::where('status', 'in_progress')->count(),
            'total_manufacturing_value' => ManufacturingOrder::sum('total_cost'),
        ];

        // Monthly trends
        $monthlyTrends = $this->getMonthlyTrends();

        return view('admin.supply-chain.analytics', compact(
            'orderAnalytics',
            'purchaseOrderAnalytics', 
            'shipmentAnalytics',
            'manufacturingAnalytics',
            'monthlyTrends'
        ));
    }

    /**
     * Calculate order fulfillment rate
     */
    private function calculateOrderFulfillmentRate()
    {
        $totalOrders = Order::count();
        $fulfilledOrders = Order::where('status', 'completed')->count();
        
        return $totalOrders > 0 ? round(($fulfilledOrders / $totalOrders) * 100, 2) : 0;
    }

    /**
     * Calculate average order processing time
     */
    private function calculateAverageOrderProcessingTime()
    {
        $completedOrders = Order::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedOrders->isEmpty()) {
            return 0;
        }

        $totalDays = $completedOrders->sum(function ($order) {
            return $order->created_at->diffInDays($order->completed_at);
        });

        return round($totalDays / $completedOrders->count(), 1);
    }

    /**
     * Calculate supplier performance score
     */
    private function calculateSupplierPerformanceScore()
    {
        // This would be a more complex calculation based on delivery times, quality, etc.
        // For now, returning a placeholder value
        return 85.5;
    }

    /**
     * Calculate inventory turnover rate
     */
    private function calculateInventoryTurnoverRate()
    {
        // This would calculate how many times inventory is sold and replaced
        // For now, returning a placeholder value
        return 12.3;
    }

    /**
     * Calculate average delivery time
     */
    private function calculateAverageDeliveryTime()
    {
        $deliveredShipments = Shipment::where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->get();

        if ($deliveredShipments->isEmpty()) {
            return 0;
        }

        $totalDays = $deliveredShipments->sum(function ($shipment) {
            return $shipment->created_at->diffInDays($shipment->delivered_at);
        });

        return round($totalDays / $deliveredShipments->count(), 1);
    }

    /**
     * Get monthly trends data
     */
    private function getMonthlyTrends()
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push([
                'month' => $date->format('M Y'),
                'orders' => Order::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'purchase_orders' => PurchaseOrder::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'shipments' => Shipment::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ]);
        }

        return $months;
    }
} 