<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Activity;
use App\Models\Vendor;
use App\Models\Inventory;

class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        // Get recent activity for the user
        $recentActivity = Activity::where('user_id', $user->user_id)
            ->latest()
            ->take(5)
            ->get();

        // Common data for all roles
        $data = [
            'recentActivity' => $recentActivity,
        ];

        // Role-specific data
        switch ($role) {
            case 'admin':
                $data['totalUsers'] = User::count();
                $data['activeUsers'] = User::where('status', 'active')->count();
                $data['pendingApprovals'] = User::where('status', 'pending')->count();
                $data['vendorApplications'] = Vendor::with('user')->orderBy('created_at', 'desc')->get();
                $data['pendingVendorApplications'] = Vendor::where('status', 'pending')->count();
                return view('dashboards.admin', $data);

            case 'farmer':
                $data['activeCrops'] = 0; // Replace with actual query
                $data['harvestReady'] = 0; // Replace with actual query
                $data['pendingOrders'] = 0; // Replace with actual query
                $data['marketPrice'] = 0.00; // Replace with actual query
                return view('dashboards.farmer', $data);

            case 'supplier':
                $vendor = Vendor::where('user_id', $user->id)->with('facilityVisits')->first();
                $data['vendor'] = $vendor;
                $query = \App\Models\Inventory::with(['product', 'warehouse'])
                    ->whereHas('product', function($query) use ($user) {
                        $query->where('supplier_id', $user->id);
                    });
                // Filtering
                if ($request->filled('search')) {
                    $search = $request->input('search');
                    $query->whereHas('product', function($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                          ->orWhere('sku', 'like', "%$search%") ;
                    });
                }
                if ($request->filled('warehouse')) {
                    $query->where('warehouse_id', $request->input('warehouse'));
                }
                if ($request->filled('status')) {
                    $status = $request->input('status');
                    if ($status === 'low') {
                        $query->where('quantity_available', '<=', 10)->where('quantity_available', '>', 0);
                    } elseif ($status === 'out') {
                        $query->where('quantity_available', '<=', 0);
                    } elseif ($status === 'in') {
                        $query->where('quantity_available', '>', 10);
                    }
                }
                $supplierInventory = $query->get();
                $data['supplierInventory'] = $supplierInventory;
                $data['filteredInventory'] = $supplierInventory;
                
                // Calculate statistics
                $data['totalInventory'] = $supplierInventory->sum('quantity_on_hand');
                $data['lowStockItems'] = $supplierInventory->where('quantity_available', '<=', 10)->where('quantity_available', '>', 0)->count();
                $data['outOfStockItems'] = $supplierInventory->where('quantity_available', '<=', 0)->count();
                $data['totalInventoryValue'] = $supplierInventory->sum(function($inv) { 
                    return $inv->quantity_on_hand * ($inv->product->cost_price ?? 0); 
                });
                
                $data['warehouses'] = \App\Models\Warehouse::all();
                $data['activeOrders'] = 0; // Replace with actual query
                $data['pendingDeliveries'] = 0; // Replace with actual query
                $data['filter_search'] = $request->input('search');
                $data['filter_warehouse'] = $request->input('warehouse');
                $data['filter_status'] = $request->input('status');
                
                // Generate inventory trend data for the last 30 days
                $trendData = [];
                for ($i = 29; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $dayKey = $date->format('Y-m-d');
                    
                    // For demo purposes, we'll simulate trend data based on current inventory
                    // In a real application, you'd have inventory movement history
                    $baseQuantity = $supplierInventory->sum('quantity_on_hand');
                    $variation = rand(-50, 50); // Simulate daily variation
                    $dayQuantity = max(0, $baseQuantity + $variation);
                    
                    $trendData[] = [
                        'date' => $date->format('M d'),
                        'total_quantity' => $dayQuantity,
                        'total_value' => $dayQuantity * ($supplierInventory->avg(function($inv) { 
                            return $inv->product->cost_price ?? 0; 
                        }) ?? 0),
                        'low_stock_count' => $supplierInventory->where('quantity_available', '<=', 10)->where('quantity_available', '>', 0)->count(),
                        'out_of_stock_count' => $supplierInventory->where('quantity_available', '<=', 0)->count(),
                    ];
                }
                $data['inventoryTrendData'] = $trendData;
                
                // Add facility visit data for notifications
                $data['scheduledVisits'] = $vendor->facilityVisits->where('status', 'scheduled');
                $data['pendingVisits'] = $vendor->facilityVisits->where('status', 'pending');
                
                return view('dashboards.supplier', $data);

            case 'manufacturer':
                $data['activeLines'] = 0; // Replace with actual query
                $data['dailyOutput'] = 0; // Replace with actual query
                $data['qualityIssues'] = 0; // Replace with actual query
                $data['rawMaterials'] = 0; // Replace with actual query
                $data['approvedSuppliers'] = User::where('role', 'supplier')
                    ->with('vendor')
                    ->whereHas('vendor', function($query) {
                        $query->where('status', 'approved');
                    })
                    ->get();
                return view('dashboards.manufacturer', $data);

            case 'distributor':
                $data['activeOrders'] = 0; // Replace with actual query
                $data['todayDeliveries'] = 0; // Replace with actual query
                $data['lowStockItems'] = 0; // Replace with actual query
                $data['totalInventory'] = 0; // Replace with actual query
                return view('dashboards.distributor', $data);

            case 'retailer':
                $data['todaySales'] = 0.00; // Replace with actual query
                $data['activeOrders'] = 0; // Replace with actual query
                $data['lowStockItems'] = 0; // Replace with actual query
                $data['totalInventory'] = 0; // Replace with actual query
                return view('dashboards.retailer', $data);

            case 'vendor':
                $vendor = Vendor::where('user_id', $user->user_id)->first();
                $data['vendor'] = $vendor;
                $data['applicationStatus'] = $vendor ? $vendor->status : 'none';
                $data['daysSinceApplication'] = $vendor ? $vendor->created_at->diffInDays(now()) : 0;
                $data['pendingDocuments'] = $vendor && $vendor->pdf_paths ? count($vendor->pdf_paths) : 0;
                $data['scheduledVisits'] = $vendor ? $vendor->facilityVisits->where('status', 'scheduled')->count() : 0;
                return view('dashboards.vendor', $data);

            default:
                return redirect()->route('login');
        }
    }

    // Individual role methods for direct access
    public function admin(Request $request)
    {
        return $this->index($request);
    }

    public function farmer(Request $request)
    {
        return $this->index($request);
    }

    public function supplier(Request $request)
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->with('facilityVisits')->first();
        
        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        // Check access level based on vendor status
        $hasFullAccess = $vendor->hasFullAccess();
        $hasBasicAccess = $vendor->hasBasicAccess();
        
        // Get recent activity
        $recentActivity = Activity::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $data = [
            'vendor' => $vendor,
            'recentActivity' => $recentActivity,
            'hasFullAccess' => $hasFullAccess,
            'hasBasicAccess' => $hasBasicAccess,
            'statusMessage' => $vendor->getStatusMessage(),
        ];

        // If PDF validation failed, show limited dashboard
        if ($vendor->status === Vendor::STATUS_PDF_REJECTED) {
            $data['pdfValidationResult'] = $vendor->pdf_validation_result;
            $data['missingSections'] = $vendor->pdf_validation_result['missingSections'] ?? [];
            return view('dashboards.supplier-pdf-rejected', $data);
        }

        // If PDF is validated but not approved, show basic dashboard
        if ($hasBasicAccess && !$hasFullAccess) {
            $data['scheduledVisits'] = $vendor->facilityVisits->where('status', 'scheduled');
            $data['pendingVisits'] = $vendor->facilityVisits->where('status', 'pending');
            return view('dashboards.supplier-basic', $data);
        }

        // If fully approved, show full dashboard with all features
        if ($hasFullAccess) {
            return $this->getFullSupplierDashboard($request, $data);
        }

        // Default: pending status
        return view('dashboards.supplier-pending', $data);
    }

    /**
     * Get full supplier dashboard with all features
     */
    private function getFullSupplierDashboard(Request $request, array $data)
    {
        $user = Auth::user();
        $vendor = $data['vendor']; // Get vendor from the passed data
        
        // Inventory data
        $query = \App\Models\Inventory::with(['product', 'warehouse'])
            ->whereHas('product', function($query) use ($user) {
                $query->where('supplier_id', $user->id);
            });
        
        // Filtering
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('sku', 'like', "%$search%");
            });
        }
        if ($request->filled('warehouse')) {
            $query->where('warehouse_id', $request->input('warehouse'));
        }
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'low') {
                $query->where('quantity_available', '<=', 10)->where('quantity_available', '>', 0);
            } elseif ($status === 'out') {
                $query->where('quantity_available', '<=', 0);
            } elseif ($status === 'in') {
                $query->where('quantity_available', '>', 10);
            }
        }
        
        $supplierInventory = $query->get();
        
        // Calculate statistics
        $data['supplierInventory'] = $supplierInventory;
        $data['filteredInventory'] = $supplierInventory;
        $data['totalInventory'] = $supplierInventory->sum('quantity_on_hand');
        $data['lowStockItems'] = $supplierInventory->where('quantity_available', '<=', 10)->where('quantity_available', '>', 0)->count();
        $data['outOfStockItems'] = $supplierInventory->where('quantity_available', '<=', 0)->count();
        $data['totalInventoryValue'] = $supplierInventory->sum(function($inv) { 
            return $inv->quantity_on_hand * ($inv->product->cost_price ?? 0); 
        });
        
        $data['warehouses'] = \App\Models\Warehouse::all();
        $data['activeOrders'] = 0; // Replace with actual query
        $data['pendingDeliveries'] = 0; // Replace with actual query
        $data['filter_search'] = $request->input('search');
        $data['filter_warehouse'] = $request->input('warehouse');
        $data['filter_status'] = $request->input('status');
        
        // Generate inventory trend data for the last 30 days
        $trendData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayKey = $date->format('Y-m-d');
            
            // For demo purposes, we'll simulate trend data based on current inventory
            $baseQuantity = $supplierInventory->sum('quantity_on_hand');
            $variation = rand(-50, 50); // Simulate daily variation
            $dayQuantity = max(0, $baseQuantity + $variation);
            
            $trendData[] = [
                'date' => $date->format('M d'),
                'total_quantity' => $dayQuantity,
                'total_value' => $dayQuantity * ($supplierInventory->avg(function($inv) { 
                    return $inv->product->cost_price ?? 0; 
                }) ?? 0),
                'low_stock_count' => $supplierInventory->where('quantity_available', '<=', 10)->where('quantity_available', '>', 0)->count(),
                'out_of_stock_count' => $supplierInventory->where('quantity_available', '<=', 0)->count(),
            ];
        }
        $data['inventoryTrendData'] = $trendData;
        
        // Add facility visit data for notifications
        $data['scheduledVisits'] = $vendor->facilityVisits->where('status', 'scheduled');
        $data['pendingVisits'] = $vendor->facilityVisits->where('status', 'pending');
        
        return view('dashboards.supplier-full', $data);
    }

    public function manufacturer(Request $request)
    {
        return $this->index($request);
    }

    public function distributor(Request $request)
    {
        $user = Auth::user();
        
        // Get distributor-specific data
        $stats = [
            'active_routes' => \App\Models\LogisticsRoute::where('status', '!=', 'Delivered')->count(),
            'today_deliveries' => \App\Models\LogisticsRoute::whereDate('created_at', today())->count(),
            'pending_shipments' => \App\Models\Shipment::whereIn('status', ['pending', 'in_transit'])->count(),
            'low_stock_items' => \App\Models\Inventory::where('quantity_available', '<=', 10)->count(),
        ];
        
        // Get recent activities
        $recentActivity = Activity::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        return view('dashboards.distributor', compact('stats', 'recentActivity'));
    }

    public function retailer(Request $request)
    {
        return $this->index($request);
    }

    public function vendor(Request $request)
    {
        return $this->index($request);
    }

    public function orders(Request $request)
    {
        return view('orders.index');
    }

    public function deliveries(Request $request)
    {
        return view('deliveries.index');
    }

    public function reports(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Get inventory data for the supplier
            $inventoryData = \App\Models\Inventory::with(['product', 'warehouse'])
                ->whereHas('product', function($query) use ($user) {
                    $query->where('supplier_id', $user->id);
                })
                ->get();

            // Get order data
            $ordersData = \App\Models\Order::with(['orderItems.product'])
                ->whereHas('orderItems.product', function($query) use ($user) {
                    $query->where('supplier_id', $user->id);
                })
                ->get();

            // Get shipment data
            $shipmentsData = \App\Models\Shipment::with(['order', 'warehouse'])
                ->whereHas('order.orderItems.product', function($query) use ($user) {
                    $query->where('supplier_id', $user->id);
                })
                ->get();

            // Calculate analytics
            $analytics = [
                'total_inventory_value' => $inventoryData->sum(function($inv) {
                    return ($inv->quantity_on_hand ?? 0) * ($inv->product->cost_price ?? 0);
                }),
                'total_orders' => $ordersData->count(),
                'total_revenue' => $ordersData->sum(function($order) {
                    return $order->orderItems->sum(function($item) {
                        return ($item->quantity ?? 0) * ($item->unit_price ?? 0);
                    });
                }),
                'total_shipments' => $shipmentsData->count(),
                'delivered_shipments' => $shipmentsData->where('status', 'delivered')->count(),
                'in_transit_shipments' => $shipmentsData->whereIn('status', ['shipped', 'in_transit'])->count(),
                'low_stock_items' => $inventoryData->where('quantity_available', '<=', 10)->count(),
                'out_of_stock_items' => $inventoryData->where('quantity_available', '<=', 0)->count(),
            ];

            // Monthly data for charts
            $monthlyData = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthKey = $date->format('Y-m');
                
                $monthlyData[$monthKey] = [
                    'month' => $date->format('M Y'),
                    'orders' => $ordersData->filter(function($order) use ($date) {
                        return $order->created_at->format('Y-m') === $date->format('Y-m');
                    })->count(),
                    'revenue' => $ordersData->filter(function($order) use ($date) {
                        return $order->created_at->format('Y-m') === $date->format('Y-m');
                    })->sum(function($order) {
                        return $order->orderItems->sum(function($item) {
                            return ($item->quantity ?? 0) * ($item->unit_price ?? 0);
                        });
                    }),
                    'shipments' => $shipmentsData->filter(function($shipment) use ($date) {
                        return $shipment->created_at->format('Y-m') === $date->format('Y-m');
                    })->count(),
                ];
            }

            // Top products by revenue
            $topProducts = $ordersData->flatMap(function($order) {
                return $order->orderItems;
            })->groupBy('product_id')->map(function($items) {
                return [
                    'product' => $items->first()->product,
                    'total_quantity' => $items->sum('quantity'),
                    'total_revenue' => $items->sum(function($item) {
                        return ($item->quantity ?? 0) * ($item->unit_price ?? 0);
                    }),
                ];
            })->sortByDesc('total_revenue')->take(5);

            // Warehouse performance
            $warehousePerformance = $inventoryData->groupBy('warehouse_id')->map(function($items, $warehouseId) {
                $warehouse = $items->first()->warehouse;
                return [
                    'warehouse' => $warehouse,
                    'total_items' => $items->count(),
                    'total_value' => $items->sum(function($inv) {
                        return ($inv->quantity_on_hand ?? 0) * ($inv->product->cost_price ?? 0);
                    }),
                    'low_stock_items' => $items->where('quantity_available', '<=', 10)->count(),
                ];
            });

        } catch (\Exception $e) {
            // If there's an error, provide default empty data
            $analytics = [
                'total_inventory_value' => 0,
                'total_orders' => 0,
                'total_revenue' => 0,
                'total_shipments' => 0,
                'delivered_shipments' => 0,
                'in_transit_shipments' => 0,
                'low_stock_items' => 0,
                'out_of_stock_items' => 0,
            ];

            $monthlyData = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthKey = $date->format('Y-m');
                
                $monthlyData[$monthKey] = [
                    'month' => $date->format('M Y'),
                    'orders' => 0,
                    'revenue' => 0,
                    'shipments' => 0,
                ];
            }

            $topProducts = collect([]);
            $warehousePerformance = collect([]);
        }

        return view('reports.index', compact('analytics', 'monthlyData', 'topProducts', 'warehousePerformance'));
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Get the same data as the reports page
            $inventoryData = \App\Models\Inventory::with(['product', 'warehouse'])
                ->whereHas('product', function($query) use ($user) {
                    $query->where('supplier_id', $user->id);
                })
                ->get();

            $ordersData = \App\Models\Order::with(['orderItems.product'])
                ->whereHas('orderItems.product', function($query) use ($user) {
                    $query->where('supplier_id', $user->id);
                })
                ->get();

            $shipmentsData = \App\Models\Shipment::with(['order', 'warehouse'])
                ->whereHas('order.orderItems.product', function($query) use ($user) {
                    $query->where('supplier_id', $user->id);
                })
                ->get();

            $analytics = [
                'total_inventory_value' => $inventoryData->sum(function($inv) {
                    return ($inv->quantity_on_hand ?? 0) * ($inv->product->cost_price ?? 0);
                }),
                'total_orders' => $ordersData->count(),
                'total_revenue' => $ordersData->sum(function($order) {
                    return $order->orderItems->sum(function($item) {
                        return ($item->quantity ?? 0) * ($item->unit_price ?? 0);
                    });
                }),
                'total_shipments' => $shipmentsData->count(),
                'delivered_shipments' => $shipmentsData->where('status', 'delivered')->count(),
                'in_transit_shipments' => $shipmentsData->whereIn('status', ['shipped', 'in_transit'])->count(),
                'low_stock_items' => $inventoryData->where('quantity_available', '<=', 10)->count(),
                'out_of_stock_items' => $inventoryData->where('quantity_available', '<=', 0)->count(),
            ];

            $topProducts = $ordersData->flatMap(function($order) {
                return $order->orderItems;
            })->groupBy('product_id')->map(function($items) {
                return [
                    'product' => $items->first()->product,
                    'total_quantity' => $items->sum('quantity'),
                    'total_revenue' => $items->sum(function($item) {
                        return ($item->quantity ?? 0) * ($item->unit_price ?? 0);
                    }),
                ];
            })->sortByDesc('total_revenue')->take(5);

            $warehousePerformance = $inventoryData->groupBy('warehouse_id')->map(function($items, $warehouseId) {
                $warehouse = $items->first()->warehouse;
                return [
                    'warehouse' => $warehouse,
                    'total_items' => $items->count(),
                    'total_value' => $items->sum(function($inv) {
                        return ($inv->quantity_on_hand ?? 0) * ($inv->product->cost_price ?? 0);
                    }),
                    'low_stock_items' => $items->where('quantity_available', '<=', 10)->count(),
                ];
            });

        } catch (\Exception $e) {
            $analytics = [
                'total_inventory_value' => 0,
                'total_orders' => 0,
                'total_revenue' => 0,
                'total_shipments' => 0,
                'delivered_shipments' => 0,
                'in_transit_shipments' => 0,
                'low_stock_items' => 0,
                'out_of_stock_items' => 0,
            ];
            $topProducts = collect([]);
            $warehousePerformance = collect([]);
        }

        // Generate PDF content
        $html = view('reports.pdf', compact('analytics', 'topProducts', 'warehousePerformance', 'user'))->render();
        
        // Return PDF response
        return response($html)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="supplier_report_' . date('Y-m-d') . '.pdf"');
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Get the same data as the reports page
            $inventoryData = \App\Models\Inventory::with(['product', 'warehouse'])
                ->whereHas('product', function($query) use ($user) {
                    $query->where('supplier_id', $user->id);
                })
                ->get();

            $ordersData = \App\Models\Order::with(['orderItems.product'])
                ->whereHas('orderItems.product', function($query) use ($user) {
                    $query->where('supplier_id', $user->id);
                })
                ->get();

            $shipmentsData = \App\Models\Shipment::with(['order', 'warehouse'])
                ->whereHas('order.orderItems.product', function($query) use ($user) {
                    $query->where('supplier_id', $user->id);
                })
                ->get();

            $analytics = [
                'total_inventory_value' => $inventoryData->sum(function($inv) {
                    return ($inv->quantity_on_hand ?? 0) * ($inv->product->cost_price ?? 0);
                }),
                'total_orders' => $ordersData->count(),
                'total_revenue' => $ordersData->sum(function($order) {
                    return $order->orderItems->sum(function($item) {
                        return ($item->quantity ?? 0) * ($item->unit_price ?? 0);
                    });
                }),
                'total_shipments' => $shipmentsData->count(),
                'delivered_shipments' => $shipmentsData->where('status', 'delivered')->count(),
                'in_transit_shipments' => $shipmentsData->whereIn('status', ['shipped', 'in_transit'])->count(),
                'low_stock_items' => $inventoryData->where('quantity_available', '<=', 10)->count(),
                'out_of_stock_items' => $inventoryData->where('quantity_available', '<=', 0)->count(),
            ];

        } catch (\Exception $e) {
            $analytics = [
                'total_inventory_value' => 0,
                'total_orders' => 0,
                'total_revenue' => 0,
                'total_shipments' => 0,
                'delivered_shipments' => 0,
                'in_transit_shipments' => 0,
                'low_stock_items' => 0,
                'out_of_stock_items' => 0,
            ];
        }

        // Generate CSV content
        $csv = "Supplier Analytics Report\n";
        $csv .= "Generated on: " . date('Y-m-d H:i:s') . "\n";
        $csv .= "Supplier: " . ($user->name ?? 'Unknown') . "\n\n";
        
        $csv .= "Key Metrics\n";
        $csv .= "Metric,Value\n";
        $csv .= "Total Revenue,\$" . number_format($analytics['total_revenue'], 2) . "\n";
        $csv .= "Total Orders," . $analytics['total_orders'] . "\n";
        $csv .= "Total Inventory Value,\$" . number_format($analytics['total_inventory_value'], 2) . "\n";
        $csv .= "Total Shipments," . $analytics['total_shipments'] . "\n";
        $csv .= "Delivered Shipments," . $analytics['delivered_shipments'] . "\n";
        $csv .= "In Transit Shipments," . $analytics['in_transit_shipments'] . "\n";
        $csv .= "Low Stock Items," . $analytics['low_stock_items'] . "\n";
        $csv .= "Out of Stock Items," . $analytics['out_of_stock_items'] . "\n";

        // Return Excel/CSV response
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="supplier_report_' . date('Y-m-d') . '.csv"');
    }

    public function exportCsv(Request $request)
    {
        return $this->exportExcel($request);
    }

    public function contracts(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Get contracts data for the supplier
            $contractsData = collect([
                [
                    'id' => 1,
                    'contract_number' => 'CTR-2024-001',
                    'title' => 'Premium Wheat Supply Agreement',
                    'customer' => 'Manufacturing Plant A',
                    'status' => 'active',
                    'start_date' => '2024-01-01',
                    'end_date' => '2024-12-31',
                    'total_value' => 150000.00,
                    'delivered_value' => 125000.00,
                    'remaining_value' => 25000.00,
                    'payment_terms' => 'Net 30',
                    'delivery_schedule' => 'Monthly',
                    'last_delivery' => '2024-11-15',
                    'next_delivery' => '2024-12-15',
                    'performance_score' => 95,
                    'risk_level' => 'low'
                ],
                [
                    'id' => 2,
                    'contract_number' => 'CTR-2024-002',
                    'title' => 'Organic Wheat Supply Contract',
                    'customer' => 'Distribution Center B',
                    'status' => 'active',
                    'start_date' => '2024-03-01',
                    'end_date' => '2025-02-28',
                    'total_value' => 200000.00,
                    'delivered_value' => 180000.00,
                    'remaining_value' => 20000.00,
                    'payment_terms' => 'Net 45',
                    'delivery_schedule' => 'Bi-weekly',
                    'last_delivery' => '2024-11-20',
                    'next_delivery' => '2024-12-04',
                    'performance_score' => 88,
                    'risk_level' => 'medium'
                ],
                [
                    'id' => 3,
                    'contract_number' => 'CTR-2024-003',
                    'title' => 'Whole Grain Wheat Agreement',
                    'customer' => 'Retail Store C',
                    'status' => 'pending',
                    'start_date' => '2024-06-01',
                    'end_date' => '2025-05-31',
                    'total_value' => 75000.00,
                    'delivered_value' => 0.00,
                    'remaining_value' => 75000.00,
                    'payment_terms' => 'Net 30',
                    'delivery_schedule' => 'Weekly',
                    'last_delivery' => null,
                    'next_delivery' => '2024-12-01',
                    'performance_score' => 0,
                    'risk_level' => 'high'
                ],
                [
                    'id' => 4,
                    'contract_number' => 'CTR-2024-004',
                    'title' => 'Durum Wheat Supply Contract',
                    'customer' => 'Processing Plant D',
                    'status' => 'completed',
                    'start_date' => '2023-09-01',
                    'end_date' => '2024-08-31',
                    'total_value' => 120000.00,
                    'delivered_value' => 120000.00,
                    'remaining_value' => 0.00,
                    'payment_terms' => 'Net 30',
                    'delivery_schedule' => 'Monthly',
                    'last_delivery' => '2024-08-15',
                    'next_delivery' => null,
                    'performance_score' => 92,
                    'risk_level' => 'low'
                ]
            ]);

            // Calculate analytics
            $analytics = [
                'total_contracts' => $contractsData->count(),
                'active_contracts' => $contractsData->where('status', 'active')->count(),
                'pending_contracts' => $contractsData->where('status', 'pending')->count(),
                'completed_contracts' => $contractsData->where('status', 'completed')->count(),
                'total_contract_value' => $contractsData->sum('total_value'),
                'delivered_value' => $contractsData->sum('delivered_value'),
                'remaining_value' => $contractsData->sum('remaining_value'),
                'average_performance' => $contractsData->where('performance_score', '>', 0)->avg('performance_score'),
                'high_risk_contracts' => $contractsData->where('risk_level', 'high')->count(),
                'upcoming_deliveries' => $contractsData->where('next_delivery', '>=', now()->format('Y-m-d'))->count(),
            ];

            // Filter contracts based on request parameters
            $filteredContracts = $contractsData;
            
            if ($request->filled('status')) {
                $filteredContracts = $filteredContracts->where('status', $request->input('status'));
            }
            
            if ($request->filled('risk_level')) {
                $filteredContracts = $filteredContracts->where('risk_level', $request->input('risk_level'));
            }
            
            if ($request->filled('search')) {
                $search = $request->input('search');
                $filteredContracts = $filteredContracts->filter(function($contract) use ($search) {
                    return str_contains(strtolower($contract['title']), strtolower($search)) ||
                           str_contains(strtolower($contract['contract_number']), strtolower($search)) ||
                           str_contains(strtolower($contract['customer']), strtolower($search));
                });
            }

        } catch (\Exception $e) {
            $analytics = [
                'total_contracts' => 0,
                'active_contracts' => 0,
                'pending_contracts' => 0,
                'completed_contracts' => 0,
                'total_contract_value' => 0,
                'delivered_value' => 0,
                'remaining_value' => 0,
                'average_performance' => 0,
                'high_risk_contracts' => 0,
                'upcoming_deliveries' => 0,
            ];
            $filteredContracts = collect([]);
        }

        return view('contracts.index', compact('analytics', 'filteredContracts'));
    }

    public function payments(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Get payments data for the supplier
            $paymentsData = collect([
                [
                    'id' => 1,
                    'invoice_number' => 'INV-2024-001',
                    'contract_number' => 'CTR-2024-001',
                    'customer' => 'Manufacturing Plant A',
                    'amount' => 25000.00,
                    'status' => 'paid',
                    'due_date' => '2024-11-15',
                    'paid_date' => '2024-11-10',
                    'payment_method' => 'Bank Transfer',
                    'reference_number' => 'BT-2024-001',
                    'days_early' => 5,
                    'late_fees' => 0.00,
                    'total_amount' => 25000.00,
                    'currency' => 'USD',
                    'notes' => 'On-time payment received'
                ],
                [
                    'id' => 2,
                    'invoice_number' => 'INV-2024-002',
                    'contract_number' => 'CTR-2024-002',
                    'customer' => 'Distribution Center B',
                    'amount' => 45000.00,
                    'status' => 'pending',
                    'due_date' => '2024-12-01',
                    'paid_date' => null,
                    'payment_method' => null,
                    'reference_number' => null,
                    'days_early' => null,
                    'late_fees' => 0.00,
                    'total_amount' => 45000.00,
                    'currency' => 'USD',
                    'notes' => 'Payment due in 5 days'
                ],
                [
                    'id' => 3,
                    'invoice_number' => 'INV-2024-003',
                    'contract_number' => 'CTR-2024-003',
                    'customer' => 'Retail Store C',
                    'amount' => 15000.00,
                    'status' => 'overdue',
                    'due_date' => '2024-11-20',
                    'paid_date' => null,
                    'payment_method' => null,
                    'reference_number' => null,
                    'days_early' => null,
                    'late_fees' => 750.00,
                    'total_amount' => 15750.00,
                    'currency' => 'USD',
                    'notes' => 'Payment overdue - late fees applied'
                ],
                [
                    'id' => 4,
                    'invoice_number' => 'INV-2024-004',
                    'contract_number' => 'CTR-2024-004',
                    'customer' => 'Processing Plant D',
                    'amount' => 30000.00,
                    'status' => 'paid',
                    'due_date' => '2024-10-15',
                    'paid_date' => '2024-10-12',
                    'payment_method' => 'Credit Card',
                    'reference_number' => 'CC-2024-004',
                    'days_early' => 3,
                    'late_fees' => 0.00,
                    'total_amount' => 30000.00,
                    'currency' => 'USD',
                    'notes' => 'Early payment with discount applied'
                ],
                [
                    'id' => 5,
                    'invoice_number' => 'INV-2024-005',
                    'contract_number' => 'CTR-2024-001',
                    'customer' => 'Manufacturing Plant A',
                    'amount' => 20000.00,
                    'status' => 'pending',
                    'due_date' => '2024-12-15',
                    'paid_date' => null,
                    'payment_method' => null,
                    'reference_number' => null,
                    'days_early' => null,
                    'late_fees' => 0.00,
                    'total_amount' => 20000.00,
                    'currency' => 'USD',
                    'notes' => 'Scheduled payment'
                ]
            ]);

            // Calculate analytics
            $analytics = [
                'total_invoices' => $paymentsData->count(),
                'paid_invoices' => $paymentsData->where('status', 'paid')->count(),
                'pending_invoices' => $paymentsData->where('status', 'pending')->count(),
                'overdue_invoices' => $paymentsData->where('status', 'overdue')->count(),
                'total_amount' => $paymentsData->sum('amount'),
                'paid_amount' => $paymentsData->where('status', 'paid')->sum('amount'),
                'pending_amount' => $paymentsData->where('status', 'pending')->sum('amount'),
                'overdue_amount' => $paymentsData->where('status', 'overdue')->sum('total_amount'),
                'average_payment_time' => $paymentsData->where('status', 'paid')->avg('days_early') ?? 0,
                'total_late_fees' => $paymentsData->sum('late_fees'),
                'upcoming_payments' => $paymentsData->where('status', 'pending')->where('due_date', '>=', now()->format('Y-m-d'))->count(),
            ];

            // Filter payments based on request parameters
            $filteredPayments = $paymentsData;
            
            if ($request->filled('status')) {
                $filteredPayments = $filteredPayments->where('status', $request->input('status'));
            }
            
            if ($request->filled('payment_method')) {
                $filteredPayments = $filteredPayments->where('payment_method', $request->input('payment_method'));
            }
            
            if ($request->filled('search')) {
                $search = $request->input('search');
                $filteredPayments = $filteredPayments->filter(function($payment) use ($search) {
                    return str_contains(strtolower($payment['invoice_number']), strtolower($search)) ||
                           str_contains(strtolower($payment['contract_number']), strtolower($search)) ||
                           str_contains(strtolower($payment['customer']), strtolower($search));
                });
            }

        } catch (\Exception $e) {
            $analytics = [
                'total_invoices' => 0,
                'paid_invoices' => 0,
                'pending_invoices' => 0,
                'overdue_invoices' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'pending_amount' => 0,
                'overdue_amount' => 0,
                'average_payment_time' => 0,
                'total_late_fees' => 0,
                'upcoming_payments' => 0,
            ];
            $filteredPayments = collect([]);
        }

        return view('payments.index', compact('analytics', 'filteredPayments'));
    }

    public function profileSettings(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Get saved preferences from session
            $savedPreferences = session('user_preferences', []);
            
            // Get user profile data
            $profileData = [
                'personal_info' => [
                    'name' => $savedPreferences['name'] ?? $user->name ?? 'John Supplier',
                    'email' => $savedPreferences['email'] ?? $user->email ?? 'john.supplier@swss.com',
                    'phone' => $savedPreferences['phone'] ?? '+1 (555) 123-4567',
                    'company' => 'Premium Wheat Suppliers Inc.',
                    'position' => 'Senior Supplier Manager',
                    'address' => $savedPreferences['address'] ?? '123 Wheat Street, Farmville, CA 90210',
                    'website' => 'www.premiumwheatsuppliers.com',
                    'bio' => $savedPreferences['bio'] ?? 'Experienced wheat supplier with over 10 years in the industry, specializing in premium quality wheat products for manufacturing and distribution.',
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($savedPreferences['name'] ?? $user->name ?? 'John Supplier') . '&background=667eea&color=fff&size=128'
                ],
                'business_info' => [
                    'business_name' => $savedPreferences['business_name'] ?? 'Premium Wheat Suppliers Inc.',
                    'business_type' => $savedPreferences['business_type'] ?? 'Corporation',
                    'tax_id' => $savedPreferences['tax_id'] ?? '12-3456789',
                    'registration_number' => $savedPreferences['registration_number'] ?? 'CA123456789',
                    'founded_year' => $savedPreferences['founded_year'] ?? '2015',
                    'employees' => '25-50',
                    'annual_revenue' => '$2.5M - $5M',
                    'certifications' => ['ISO 9001', 'HACCP', 'Organic Certified'],
                    'specializations' => ['Premium Wheat', 'Organic Wheat', 'Durum Wheat', 'Whole Grain']
                ],
                'contact_preferences' => [
                    'email_notifications' => $savedPreferences['email_notifications'] ?? true,
                    'sms_notifications' => $savedPreferences['sms_notifications'] ?? false,
                    'push_notifications' => $savedPreferences['push_notifications'] ?? true,
                    'marketing_emails' => $savedPreferences['marketing_emails'] ?? false,
                    'order_updates' => $savedPreferences['order_updates'] ?? true,
                    'payment_reminders' => $savedPreferences['payment_reminders'] ?? true,
                    'system_alerts' => $savedPreferences['system_alerts'] ?? true
                ],
                'security_settings' => [
                    'two_factor_enabled' => true,
                    'last_password_change' => '2024-10-15',
                    'last_login' => now()->format('Y-m-d H:i:s'),
                    'login_history' => [
                        ['date' => '2024-11-25 14:30:00', 'ip' => '192.168.1.100', 'device' => 'Chrome on Windows'],
                        ['date' => '2024-11-24 09:15:00', 'ip' => '192.168.1.100', 'device' => 'Chrome on Windows'],
                        ['date' => '2024-11-23 16:45:00', 'ip' => '192.168.1.100', 'device' => 'Mobile Safari']
                    ]
                ],
                'account_stats' => [
                    'member_since' => '2020-03-15',
                    'total_orders' => 156,
                    'total_contracts' => 12,
                    'total_revenue' => 1250000.00,
                    'average_rating' => 4.8,
                    'response_time' => '2.3 hours',
                    'on_time_delivery' => 98.5
                ]
            ];

        } catch (\Exception $e) {
            $profileData = [
                'personal_info' => [],
                'business_info' => [],
                'contact_preferences' => [],
                'security_settings' => [],
                'account_stats' => []
            ];
        }

        return view('profile-settings.index', compact('profileData'));
    }

    public function saveProfileSettings(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Validate the request
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'bio' => 'nullable|string|max:1000',
                'business_name' => 'nullable|string|max:255',
                'business_type' => 'nullable|string|max:100',
                'tax_id' => 'nullable|string|max:50',
                'registration_number' => 'nullable|string|max:100',
                'founded_year' => 'nullable|integer|min:1900|max:' . date('Y'),
                'email_notifications' => 'boolean',
                'sms_notifications' => 'boolean',
                'push_notifications' => 'boolean',
                'marketing_emails' => 'boolean',
                'order_updates' => 'boolean',
                'payment_reminders' => 'boolean',
                'system_alerts' => 'boolean',
            ]);

            // Update user basic information
            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ]);

            // Store additional profile data in user meta or separate table
            // For now, we'll store it in a JSON column or use a simple approach
            // In a real application, you might want to create separate tables for this data
            
            // Store preferences in session for now (in production, use database)
            $preferences = [
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'bio' => $request->input('bio'),
                'business_name' => $request->input('business_name'),
                'business_type' => $request->input('business_type'),
                'tax_id' => $request->input('tax_id'),
                'registration_number' => $request->input('registration_number'),
                'founded_year' => $request->input('founded_year'),
                'email_notifications' => $request->boolean('email_notifications'),
                'sms_notifications' => $request->boolean('sms_notifications'),
                'push_notifications' => $request->boolean('push_notifications'),
                'marketing_emails' => $request->boolean('marketing_emails'),
                'order_updates' => $request->boolean('order_updates'),
                'payment_reminders' => $request->boolean('payment_reminders'),
                'system_alerts' => $request->boolean('system_alerts'),
            ];

            // Store in session for demonstration (in production, save to database)
            session(['user_preferences' => $preferences]);

            return redirect()->back()->with('success', 'Profile settings saved successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save profile settings. Please try again.');
        }
    }

    public function vendors()
    {
        $vendors = \App\Models\Vendor::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.vendors', compact('vendors'));
    }

    public function logistics(Request $request)
    {
        return view('logistics');
    }

    public function analytics(Request $request)
    {
        return view('analytics');
    }
}