<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display reports dashboard
     */
    public function index()
    {
        try {
            // Get date range from request or default to last 30 days
            $dateRange = request('date_range', '30');
            $startDate = Carbon::now()->subDays($dateRange);
            $endDate = Carbon::now();

            // User Statistics
            $userStats = [
                'total_users' => User::count(),
                'new_users_this_period' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
                'active_users' => User::where('email_verified_at', '!=', null)->count(),
                'users_by_role' => User::select('role', DB::raw('count(*) as count'))
                    ->groupBy('role')
                    ->get()
                    ->pluck('count', 'role')
                    ->toArray(),
            ];

            // System Activity Statistics
            $activityStats = [
                'total_activities' => Activity::whereBetween('created_at', [$startDate, $endDate])->count(),
                'activities_by_type' => Activity::select('type', DB::raw('count(*) as count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('type')
                    ->get()
                    ->pluck('count', 'type')
                    ->toArray(),
                'top_users' => Activity::select('user_id', DB::raw('count(*) as activity_count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('user_id')
                    ->orderBy('activity_count', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($activity) {
                        try {
                            $activity->user = User::find($activity->user_id);
                            return $activity;
                        } catch (\Exception $e) {
                            $activity->user = null;
                            return $activity;
                        }
                    }),
            ];

            // Vendor Statistics
            $vendorStats = [
                'total_vendors' => Vendor::count(),
                'pending_vendors' => Vendor::where('status', 'pending')->count(),
                'approved_vendors' => Vendor::where('status', 'approved')->count(),
                'rejected_vendors' => Vendor::where('status', 'rejected')->count(),
                'vendors_by_status' => Vendor::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get()
                    ->pluck('count', 'status')
                    ->toArray(),
            ];

            // Product and Inventory Statistics
            $productStats = [
                'total_products' => Product::count(),
                'low_stock_products' => Inventory::where('quantity_on_hand', '<', 10)->count(),
                'out_of_stock_products' => Inventory::where('quantity_on_hand', 0)->count(),
                'total_inventory_value' => Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                    ->selectRaw('SUM(inventories.quantity_on_hand * products.unit_price) as total_value')
                    ->first()->total_value ?? 0,
            ];

            // Order Statistics
            $orderStats = [
                'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
                'completed_orders' => Order::where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'pending_orders' => Order::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_revenue' => Order::where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_amount'),
            ];

            // Recent Activities for Timeline
            $recentActivities = Activity::whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($activity) {
                    try {
                        $activity->user = User::find($activity->user_id);
                        return $activity;
                    } catch (\Exception $e) {
                        $activity->user = null;
                        return $activity;
                    }
                });

            // Chart Data
            $chartData = [
                'user_growth' => $this->getUserGrowthData($startDate, $endDate),
                'activity_trends' => $this->getActivityTrendsData($startDate, $endDate),
                'vendor_applications' => $this->getVendorApplicationsData($startDate, $endDate),
                'revenue_trends' => $this->getRevenueTrendsData($startDate, $endDate),
            ];

            return view('admin.reports.index', compact(
                'userStats',
                'activityStats',
                'vendorStats',
                'productStats',
                'orderStats',
                'recentActivities',
                'chartData',
                'dateRange',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            // Return a simple error view or redirect with error message
            return redirect()->back()->with('error', 'Unable to load reports. Please try again later.');
        }
    }

    /**
     * Generate user growth data for charts
     */
    private function getUserGrowthData($startDate, $endDate)
    {
        $data = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $count = User::whereDate('created_at', $currentDate)->count();
            $data[] = [
                'date' => $currentDate->format('M d'),
                'count' => $count
            ];
            $currentDate->addDay();
        }

        return $data;
    }

    /**
     * Generate activity trends data for charts
     */
    private function getActivityTrendsData($startDate, $endDate)
    {
        $data = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $count = Activity::whereDate('created_at', $currentDate)->count();
            $data[] = [
                'date' => $currentDate->format('M d'),
                'count' => $count
            ];
            $currentDate->addDay();
        }

        return $data;
    }

    /**
     * Generate vendor applications data for charts
     */
    private function getVendorApplicationsData($startDate, $endDate)
    {
        $data = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $count = Vendor::whereDate('created_at', $currentDate)->count();
            $data[] = [
                'date' => $currentDate->format('M d'),
                'count' => $count
            ];
            $currentDate->addDay();
        }

        return $data;
    }

    /**
     * Generate revenue trends data for charts
     */
    private function getRevenueTrendsData($startDate, $endDate)
    {
        $data = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $revenue = Order::where('status', 'completed')
                ->whereDate('created_at', $currentDate)
                ->sum('total_amount');
            $data[] = [
                'date' => $currentDate->format('M d'),
                'revenue' => $revenue
            ];
            $currentDate->addDay();
        }

        return $data;
    }

    /**
     * Export reports as PDF
     */
    public function exportPdf(Request $request)
    {
        $reportType = $request->get('type', 'overview');
        $dateRange = $request->get('date_range', '30');
        
        // Generate PDF report based on type
        // This would typically use a PDF library like DomPDF
        
        return response()->json(['message' => 'PDF export functionality would be implemented here']);
    }

    /**
     * Export reports as Excel
     */
    public function exportExcel(Request $request)
    {
        $reportType = $request->get('type', 'overview');
        $dateRange = $request->get('date_range', '30');
        
        // Generate Excel report based on type
        // This would typically use a library like PhpSpreadsheet
        
        return response()->json(['message' => 'Excel export functionality would be implemented here']);
    }

    /**
     * Export reports as CSV
     */
    public function exportCsv(Request $request)
    {
        $reportType = $request->get('type', 'overview');
        $dateRange = $request->get('date_range', '30');
        
        // Generate CSV report based on type
        
        return response()->json(['message' => 'CSV export functionality would be implemented here']);
    }

    /**
     * Get detailed user report
     */
    public function userReport(Request $request)
    {
        $users = User::with('activities')
            ->when($request->filled('role'), function ($query) use ($request) {
                return $query->where('role', $request->role);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'verified') {
                    return $query->whereNotNull('email_verified_at');
                } else {
                    return $query->whereNull('email_verified_at');
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reports.user-report', compact('users'));
    }

    /**
     * Get detailed activity report
     */
    public function activityReport(Request $request)
    {
        $activities = Activity::with('user')
            ->when($request->filled('type'), function ($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->when($request->filled('user_id'), function ($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.reports.activity-report', compact('activities'));
    }

    /**
     * Get detailed vendor report
     */
    public function vendorReport(Request $request)
    {
        $vendors = Vendor::with('user')
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reports.vendor-report', compact('vendors'));
    }
} 