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
                $vendor = Vendor::where('user_id', $user->user_id)->with('facilityVisits')->first();
                $data['vendor'] = $vendor;
                $query = \App\Models\Inventory::with(['product', 'warehouse'])
                    ->whereHas('product', function($query) use ($user) {
                        $query->where('supplier_id', $user->user_id);
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
                $data['totalInventory'] = $supplierInventory->sum('quantity_on_hand');
                $data['lowStockItems'] = $supplierInventory->where('quantity_available', '<=', 10)->count();
                $data['outOfStockItems'] = $supplierInventory->where('quantity_available', '<=', 0)->count();
                $data['totalInventoryValue'] = $supplierInventory->sum(function($inv) { return $inv->quantity_on_hand * ($inv->product->cost_price ?? 0); });
                $data['warehouses'] = \App\Models\Warehouse::all();
                $data['activeOrders'] = 0; // Replace with actual query
                $data['pendingDeliveries'] = 0; // Replace with actual query
                $data['filter_search'] = $request->input('search');
                $data['filter_warehouse'] = $request->input('warehouse');
                $data['filter_status'] = $request->input('status');
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
        return $this->index($request);
    }

    public function manufacturer(Request $request)
    {
        return $this->index($request);
    }

    public function distributor(Request $request)
    {
        return $this->index($request);
    }

    public function retailer(Request $request)
    {
        return $this->index($request);
    }

    public function vendor(Request $request)
    {
        return $this->index($request);
    }
}