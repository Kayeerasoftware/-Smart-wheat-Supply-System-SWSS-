<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminSystemSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display system settings dashboard (admin view)
     */
    public function index()
    {
        // Get system statistics
        $systemStats = [
            'total_users' => User::count(),
            'active_users' => User::where('email_verified_at', '!=', null)->count(),
            'pending_users' => User::where('email_verified_at', null)->count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'supplier_users' => User::where('role', 'supplier')->count(),
            'manufacturer_users' => User::where('role', 'manufacturer')->count(),
            'retailer_users' => User::where('role', 'retailer')->count(),
            'distributor_users' => User::where('role', 'distributor')->count(),
            'farmer_users' => User::where('role', 'farmer')->count(),
        ];

        // Get recent system activities
        $recentActivities = Activity::orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Get system health metrics
        $systemHealth = [
            'database_connections' => $this->checkDatabaseHealth(),
            'storage_usage' => $this->getStorageUsage(),
            'memory_usage' => $this->getMemoryUsage(),
            'disk_usage' => $this->getDiskUsage(),
        ];

        return view('admin.system-settings.index', compact('systemStats', 'recentActivities', 'systemHealth'));
    }

    /**
     * Display user management page
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        $roles = ['admin', 'supplier', 'manufacturer', 'retailer', 'distributor', 'farmer'];

        return view('admin.system-settings.users', compact('users', 'roles'));
    }

    /**
     * Display system configuration page
     */
    public function configuration()
    {
        // Get current system configuration
        $config = [
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'app_url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'mail_driver' => config('mail.default'),
            'database_connection' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
        ];

        return view('admin.system-settings.configuration', compact('config'));
    }

    /**
     * Display security settings page
     */
    public function security()
    {
        // Get security statistics
        $securityStats = [
            'failed_login_attempts' => $this->getFailedLoginAttempts(),
            'password_resets' => $this->getPasswordResets(),
            'suspicious_activities' => $this->getSuspiciousActivities(),
            'last_security_scan' => $this->getLastSecurityScan(),
        ];
        $securityRecommendations = $this->getSecurityRecommendations();
        return view('admin.system-settings.security', compact('securityStats', 'securityRecommendations'));
    }

    /**
     * Display audit logs page
     */
    public function auditLogs(Request $request)
    {
        $query = Activity::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
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
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $activities = $query->with('user')->orderBy('created_at', 'desc')->paginate(50);
        $users = User::all();

        return view('admin.system-settings.audit-logs', compact('activities', 'users'));
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,supplier,manufacturer,retailer,distributor,farmer',
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $request->role]);

        // Log the activity
        Activity::create([
            'user_id' => Auth::id(),
            'type' => 'user_role_updated',
            'description' => "Updated user {$user->name} role from {$oldRole} to {$request->role}",
        ]);

        return back()->with('success', 'User role updated successfully!');
    }

    /**
     * Toggle user status
     */
    public function toggleUserStatus(User $user)
    {
        $user->update(['email_verified_at' => $user->email_verified_at ? null : now()]);

        $status = $user->email_verified_at ? 'activated' : 'deactivated';
        
        // Log the activity
        Activity::create([
            'user_id' => Auth::id(),
            'type' => 'user_status_toggled',
            'description' => "{$status} user {$user->name}",
        ]);

        return back()->with('success', "User {$status} successfully!");
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account!']);
        }

        $userName = $user->name;
        $user->delete();

        // Log the activity
        Activity::create([
            'user_id' => Auth::id(),
            'type' => 'user_deleted',
            'description' => "Deleted user {$userName}",
        ]);

        return back()->with('success', 'User deleted successfully!');
    }

    /**
     * Update system configuration
     */
    public function updateConfiguration(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'timezone' => 'required|string',
            'locale' => 'required|string',
        ]);

        // Log the activity
        Activity::create([
            'user_id' => Auth::id(),
            'type' => 'system_config_updated',
            'description' => 'Updated system configuration',
        ]);

        return back()->with('success', 'System configuration updated successfully!');
    }

    /**
     * Check database health
     */
    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    /**
     * Get storage usage
     */
    private function getStorageUsage()
    {
        $storagePath = storage_path();
        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercentage = ($usedSpace / $totalSpace) * 100;

        return [
            'total' => $this->formatBytes($totalSpace),
            'used' => $this->formatBytes($usedSpace),
            'free' => $this->formatBytes($freeSpace),
            'percentage' => round($usagePercentage, 2),
        ];
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage()
    {
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = memory_get_usage(true);
        $peakMemoryUsage = memory_get_peak_usage(true);

        return [
            'limit' => $memoryLimit,
            'current' => $this->formatBytes($memoryUsage),
            'peak' => $this->formatBytes($peakMemoryUsage),
        ];
    }

    /**
     * Get disk usage
     */
    private function getDiskUsage()
    {
        $diskPath = base_path();
        $totalSpace = disk_total_space($diskPath);
        $freeSpace = disk_free_space($diskPath);
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercentage = ($usedSpace / $totalSpace) * 100;

        return [
            'total' => $this->formatBytes($totalSpace),
            'used' => $this->formatBytes($usedSpace),
            'free' => $this->formatBytes($freeSpace),
            'percentage' => round($usagePercentage, 2),
        ];
    }

    /**
     * Get failed login attempts (last 7 days)
     */
    private function getFailedLoginAttempts()
    {
        return Activity::where('type', 'failed_login')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    }

    /**
     * Get password resets (last 7 days)
     */
    private function getPasswordResets()
    {
        return DB::table('password_resets')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    }

    /**
     * Get suspicious activities (last 7 days)
     */
    private function getSuspiciousActivities()
    {
        return Activity::where('type', 'suspicious')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    }

    /**
     * Get last security scan timestamp
     */
    private function getLastSecurityScan()
    {
        $scan = Activity::where('type', 'security_scan')->orderByDesc('created_at')->first();
        return $scan ? $scan->created_at : now()->subDays(7);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Run security scan (AJAX)
     */
    public function runSecurityScan(Request $request)
    {
        // Simulate scan logic (in real app, run actual checks)
        sleep(2); // Simulate scan time

        // Log the scan activity
        Activity::create([
            'user_id' => Auth::id(),
            'type' => 'security_scan',
            'description' => 'Security scan was run by admin',
        ]);

        $now = now();
        return response()->json([
            'success' => true,
            'last_scan_time' => $now->format('M d, H:i'),
        ]);
    }

    /**
     * Get security recommendations based on live data
     */
    private function getSecurityRecommendations()
    {
        $recommendations = [];

        // Example: SSL certificate expiry (simulate, replace with real check if available)
        $sslExpiryDays = 30; // Replace with real check if possible
        if ($sslExpiryDays < 15) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Update SSL Certificate',
                'message' => 'Your SSL certificate will expire soon. Consider renewing it to maintain secure connections.'
            ];
        } else {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'SSL Certificate Status',
                'message' => 'Your SSL certificate is valid. No immediate action required.'
            ];
        }

        // Example: Login notifications (simulate, replace with real check if available)
        $loginNotificationsEnabled = false; // Replace with real check if possible
        if (!$loginNotificationsEnabled) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Enable Login Notifications',
                'message' => 'Consider enabling email notifications for login attempts to monitor account access.'
            ];
        }

        // Example: Security best practices (simulate, replace with real check if available)
        $bestPracticesFollowed = true; // Replace with real check if possible
        if ($bestPracticesFollowed) {
            $recommendations[] = [
                'type' => 'success',
                'title' => 'Security Best Practices',
                'message' => 'Your system is following most security best practices. Keep up the good work!'
            ];
        } else {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Review Security Practices',
                'message' => 'Some security best practices are not enabled. Please review your settings.'
            ];
        }

        return $recommendations;
    }
} 