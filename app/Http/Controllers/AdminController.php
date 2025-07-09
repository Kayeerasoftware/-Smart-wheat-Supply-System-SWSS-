<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor;
use App\Models\FacilityVisit;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Shipment;
use App\Models\Supplier;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('admin'); // TODO: Create admin middleware
    }

    public function scheduleFacilityVisit(Request $request, $vendorId)
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $vendor = Vendor::findOrFail($vendorId);
        
        // Create facility visit
        $visit = FacilityVisit::create([
            'vendor_id' => $vendor->id,
            'scheduled_at' => $request->scheduled_at,
            'status' => 'scheduled',
            'notes' => $request->notes,
        ]);

        // Send notification to supplier
        try {
            \App\Services\NotificationService::sendFacilityVisitNotification($visit, 'scheduled');
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send facility visit notification: ' . $e->getMessage());
        }

        // Update vendor status
        $vendor->update(['status' => 'pending_visit']);

        return response()->json([
            'success' => true,
            'message' => 'Facility visit scheduled successfully'
        ]);
    }

    public function approveVendor($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        
        DB::transaction(function () use ($vendor) {
            // Update vendor status
            $vendor->update(['status' => 'approved']);
            
            // Change user role from vendor to supplier
            $user = $vendor->user;
            $user->update(['role' => 'supplier']);
            
            // Update facility visit outcome
            $latestVisit = $vendor->facilityVisits()->latest()->first();
            if ($latestVisit) {
                $latestVisit->update([
                    'status' => 'completed',
                    'outcome' => 'approved'
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Vendor approved successfully. Role changed to supplier.'
        ]);
    }

    public function rejectVendor(Request $request, $vendorId)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $vendor = Vendor::findOrFail($vendorId);
        
        DB::transaction(function () use ($vendor, $request) {
            // Update vendor status
            $vendor->update(['status' => 'rejected']);
            
            // Update facility visit outcome
            $latestVisit = $vendor->facilityVisits()->latest()->first();
            if ($latestVisit) {
                $latestVisit->update([
                    'status' => 'completed',
                    'outcome' => 'rejected'
                ]);
            }
            
            // TODO: Send rejection notification to vendor
        });

        return response()->json([
            'success' => true,
            'message' => 'Vendor rejected successfully'
        ]);
    }

    public function viewVendorDetails($vendorId)
    {
        $vendor = Vendor::with(['user', 'facilityVisits'])->findOrFail($vendorId);
        
        return response()->json([
            'success' => true,
            'vendor' => $vendor,
            'application_data' => $vendor->application_data,
            'pdf_paths' => $vendor->pdf_paths,
            'facility_visits' => $vendor->facilityVisits,
            'scores' => [
                'financial' => $vendor->score_financial,
                'reputation' => $vendor->score_reputation,
                'compliance' => $vendor->score_compliance,
                'total' => $vendor->total_score,
            ]
        ]);
    }

    public function updateVendorScores(Request $request, $vendorId)
    {
        $request->validate([
            'score_financial' => 'required|numeric|min:0|max:100',
            'score_reputation' => 'required|numeric|min:0|max:100',
            'score_compliance' => 'required|numeric|min:0|max:100',
        ]);

        $vendor = Vendor::findOrFail($vendorId);
        
        $totalScore = ($request->score_financial * 0.4) + 
                     ($request->score_reputation * 0.3) + 
                     ($request->score_compliance * 0.3);
        
        $vendor->update([
            'score_financial' => $request->score_financial,
            'score_reputation' => $request->score_reputation,
            'score_compliance' => $request->score_compliance,
            'total_score' => $totalScore,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor scores updated successfully',
            'total_score' => $totalScore
        ]);
    }

    public function vendors()
    {
        $vendors = Vendor::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.vendors', compact('vendors'));
    }

    public function analytics()
    {
        // User statistics
        $totalUsers = User::count();
        $activeUsers = User::count(); // Simplified - all users are considered active
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role');

        // User growth percentage (current month vs previous month)
        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonth()->format('Y-m');
        $currentMonthUsers = User::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])->count();
        $previousMonthUsers = User::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$previousMonth])->count();
        if ($previousMonthUsers > 0) {
            $userGrowthPercent = round((($currentMonthUsers - $previousMonthUsers) / $previousMonthUsers) * 100);
        } else {
            $userGrowthPercent = $currentMonthUsers > 0 ? 100 : 0;
        }

        // Vendor statistics
        $totalVendors = Vendor::count();
        $pendingVendors = Vendor::where('status', 'pending')->count();
        $approvedVendors = Vendor::where('status', 'approved')->count();
        $rejectedVendors = Vendor::where('status', 'rejected')->count();

        // Product statistics
        $totalProducts = Product::count();
        $lowStockProducts = Product::whereHas('inventories', function($query) {
            $query->where('quantity_available', '<=', DB::raw('products.reorder_point'));
        })->count();

        // Order statistics
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();

        // Inventory statistics
        $totalInventoryItems = Inventory::count();
        $totalWarehouses = Warehouse::count();

        // Monthly trends (last 6 months)
        $monthlyUsers = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyVendors = Vendor::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyOrders = Order::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top performing vendors
        $topVendors = Vendor::orderBy('total_score', 'desc')
            ->with('user')
            ->limit(5)
            ->get();

        // Recent activities
        $recentActivities = DB::table('activities')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics', compact(
            'totalUsers',
            'activeUsers',
            'usersByRole',
            'totalVendors',
            'pendingVendors',
            'approvedVendors',
            'rejectedVendors',
            'totalProducts',
            'lowStockProducts',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalInventoryItems',
            'totalWarehouses',
            'monthlyUsers',
            'monthlyVendors',
            'monthlyOrders',
            'topVendors',
            'recentActivities',
            'userGrowthPercent'
        ));
    }

    public function updateSettings(\Illuminate\Http\Request $request)
    {
        \App\Models\Setting::set('require_2fa', $request->has('require_2fa') ? '1' : '0');
        \App\Models\Setting::set('require_strong_passwords', $request->has('require_strong_passwords') ? '1' : '0');
        \App\Models\Setting::set('auto_logout', $request->input('auto_logout', 30));
        \App\Models\Setting::set('login_notifications', $request->has('login_notifications') ? '1' : '0');

        return back()->with('status', 'Settings updated!');
    }

    public function securityStatus()
    {
        // System Status: check DB connection
        try {
            \DB::connection()->getPdo();
            $systemStatus = 'All systems operational';
        } catch (\Exception $e) {
            $systemStatus = 'Issues detected';
        }

        // Firewall Status (Windows/Linux)
        $firewallStatus = 'Unknown';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec('netsh advfirewall show allprofiles');
            $firewallStatus = (strpos($output, 'State ON') !== false) ? 'Firewall is active and protecting' : 'Firewall is not active';
        } else {
            $output = shell_exec('sudo ufw status');
            $firewallStatus = (strpos($output, 'Status: active') !== false) ? 'Firewall is active and protecting' : 'Firewall is not active';
        }

        // SSL Certificate (for current host)
        $sslStatus = 'Unknown';
        $host = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $context = stream_context_create(["ssl" => ["capture_peer_cert" => true]]);
        $client = @stream_socket_client("ssl://$host:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        if ($client) {
            $cont = stream_context_get_params($client);
            $cert = openssl_x509_parse($cont["options"]["ssl"]["peer_certificate"]);
            $validTo = $cert['validTo_time_t'];
            $daysLeft = floor(($validTo - time()) / 86400);
            $sslStatus = "Certificate expires in $daysLeft days";
        } else {
            $sslStatus = "Could not check certificate";
        }

        // Backup Status (storage/app/backups)
        $backupStatus = 'Unknown';
        $backupDir = storage_path('app/backups');
        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*');
            if ($files) {
                $lastBackup = max($files);
                $lastBackupTime = filemtime($lastBackup);
                $backupStatus = 'Last backup: ' . \Carbon\Carbon::createFromTimestamp($lastBackupTime)->diffForHumans();
            } else {
                $backupStatus = 'No backups found';
            }
        } else {
            $backupStatus = 'Backup directory not found';
        }

        return response()->json([
            'system_status' => $systemStatus,
            'firewall_status' => $firewallStatus,
            'ssl_status' => $sslStatus,
            'backup_status' => $backupStatus,
        ]);
    }

    /**
     * Export all vendors as CSV
     */
    public function exportVendorsCsv()
    {
        $vendors = \App\Models\Vendor::with('user')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vendors.csv"',
        ];

        $callback = function() use ($vendors) {
            $handle = fopen('php://output', 'w');
            // CSV header
            fputcsv($handle, ['Business Name', 'Business Type', 'Email', 'Phone', 'Status', 'Score', 'Applied']);
            foreach ($vendors as $vendor) {
                fputcsv($handle, [
                    $vendor->application_data['business_name'] ?? $vendor->user->name ?? 'N/A',
                    $vendor->application_data['business_type'] ?? 'N/A',
                    $vendor->user->email ?? 'N/A',
                    $vendor->application_data['phone'] ?? 'N/A',
                    $vendor->status,
                    $vendor->total_score ?? 'N/A',
                    $vendor->created_at->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export dashboard stats as CSV
     */
    public function exportDashboardCsv()
    {
        $totalUsers = \App\Models\User::count();
        $totalVendors = \App\Models\Vendor::count();
        $totalOrders = \App\Models\Order::count();
        $totalInventory = \App\Models\Inventory::count();
        $totalShipments = \App\Models\Shipment::count();
        $totalProducts = \App\Models\Product::count();
        $totalSuppliers = \App\Models\Supplier::count();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="dashboard_stats.csv"',
        ];

        $callback = function() use ($totalUsers, $totalVendors, $totalOrders, $totalInventory, $totalShipments, $totalProducts, $totalSuppliers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Total Users', $totalUsers]);
            fputcsv($handle, ['Total Vendors', $totalVendors]);
            fputcsv($handle, ['Total Orders', $totalOrders]);
            fputcsv($handle, ['Total Inventory Items', $totalInventory]);
            fputcsv($handle, ['Total Shipments', $totalShipments]);
            fputcsv($handle, ['Total Products', $totalProducts]);
            fputcsv($handle, ['Total Suppliers', $totalSuppliers]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Return system performance data (activity count per day for 7/30 days, per week for 90 days)
     */
    public function getSystemPerformanceData(Request $request)
    {
        $range = (int) $request->query('range', 30);
        $labels = [];
        $data = [];
        $now = now();
        if ($range === 90) {
            // Aggregate by week (13 weeks)
            for ($i = 12; $i >= 0; $i--) {
                $startOfWeek = $now->copy()->subWeeks($i)->startOfWeek();
                $endOfWeek = $startOfWeek->copy()->endOfWeek();
                $labels[] = $startOfWeek->format('M d');
                if (class_exists('App\\Models\\Activity')) {
                    $count = \App\Models\Activity::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                } else {
                    $count = \App\Models\Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                }
                $data[] = $count;
            }
        } else {
            // Daily for 7/30 days
            for ($i = $range - 1; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $labels[] = $date->format('M d');
                if (class_exists('App\\Models\\Activity')) {
                    $count = \App\Models\Activity::whereDate('created_at', $date->toDateString())->count();
                } else {
                    $count = \App\Models\Order::whereDate('created_at', $date->toDateString())->count();
                }
                $data[] = $count;
            }
        }
        return response()->json(['labels' => $labels, 'data' => $data]);
    }
}
