<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminAuditLogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display audit logs dashboard
     */
    public function index()
    {
        // Get date range from request or default to last 30 days
        $dateRange = request('date_range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();

        try {
            // Security Events Statistics
            $securityStats = [
                'total_events' => Activity::whereBetween('created_at', [$startDate, $endDate])->count(),
                'login_events' => Activity::where('type', 'login')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'failed_logins' => Activity::where('type', 'failed_login')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'suspicious_activities' => Activity::where('type', 'suspicious')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'data_access_events' => Activity::where('type', 'data_access')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'system_changes' => Activity::where('type', 'system_change')->whereBetween('created_at', [$startDate, $endDate])->count(),
            ];

            // User Activity Statistics
            $userActivityStats = [
                'active_users_today' => User::whereDate('updated_at', Carbon::today())->count(),
                'active_users_week' => User::whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
                'inactive_users' => User::where('updated_at', '<', Carbon::now()->subDays(30))->count(),
                'new_users_this_period' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
                'top_active_users' => Activity::select('user_id', DB::raw('count(*) as activity_count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('user_id')
                    ->orderBy('activity_count', 'desc')
                    ->limit(10)
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

            // System Health Statistics
            $systemHealthStats = [
                'total_system_events' => Activity::where('type', 'system')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'error_events' => Activity::where('type', 'error')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'warning_events' => Activity::where('type', 'warning')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'info_events' => Activity::where('type', 'info')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'critical_events' => Activity::where('type', 'critical')->whereBetween('created_at', [$startDate, $endDate])->count(),
            ];

            // Recent Audit Logs
            $recentLogs = Activity::whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->limit(50)
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
                'security_events_trend' => $this->getSecurityEventsTrendData($startDate, $endDate),
                'user_activity_trend' => $this->getUserActivityTrendData($startDate, $endDate),
                'system_health_trend' => $this->getSystemHealthTrendData($startDate, $endDate),
                'event_types_distribution' => $this->getEventTypesDistributionData($startDate, $endDate),
            ];

            return view('admin.audit-logs.index', compact(
                'securityStats',
                'userActivityStats',
                'systemHealthStats',
                'recentLogs',
                'chartData',
                'dateRange',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to load audit logs. Please try again later.');
        }
    }

    /**
     * Generate security events trend data for charts
     */
    private function getSecurityEventsTrendData($startDate, $endDate)
    {
        $data = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $count = Activity::whereIn('type', ['login', 'failed_login', 'suspicious', 'data_access'])
                ->whereDate('created_at', $currentDate)
                ->count();
            $data[] = [
                'date' => $currentDate->format('M d'),
                'count' => $count
            ];
            $currentDate->addDay();
        }

        return $data;
    }

    /**
     * Generate user activity trend data for charts
     */
    private function getUserActivityTrendData($startDate, $endDate)
    {
        $data = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $count = Activity::where('type', 'user_activity')
                ->whereDate('created_at', $currentDate)
                ->count();
            $data[] = [
                'date' => $currentDate->format('M d'),
                'count' => $count
            ];
            $currentDate->addDay();
        }

        return $data;
    }

    /**
     * Generate system health trend data for charts
     */
    private function getSystemHealthTrendData($startDate, $endDate)
    {
        $data = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $count = Activity::whereIn('type', ['system', 'error', 'warning', 'critical'])
                ->whereDate('created_at', $currentDate)
                ->count();
            $data[] = [
                'date' => $currentDate->format('M d'),
                'count' => $count
            ];
            $currentDate->addDay();
        }

        return $data;
    }

    /**
     * Generate event types distribution data for charts
     */
    private function getEventTypesDistributionData($startDate, $endDate)
    {
        return Activity::select('type', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Get detailed security logs
     */
    public function securityLogs(Request $request)
    {
        $logs = Activity::whereIn('type', ['login', 'failed_login', 'suspicious', 'data_access', 'system_change'])
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

        $users = \App\Models\User::all();
        return view('admin.audit-logs.security-logs', compact('logs', 'users'));
    }

    /**
     * Get detailed user activity logs
     */
    public function userActivityLogs(Request $request)
    {
        $logs = Activity::where('type', 'user_activity')
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

        $users = \App\Models\User::orderBy('username')->get();
        return view('admin.audit-logs.user-activity-logs', compact('logs', 'users'));
    }

    /**
     * Get detailed system logs
     */
    public function systemLogs(Request $request)
    {
        $logs = Activity::whereIn('type', ['system', 'error', 'warning', 'critical', 'info'])
            ->when($request->filled('type'), function ($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.audit-logs.system-logs', compact('logs'));
    }

    /**
     * Export audit logs as PDF (matching the current page and filters)
     */
    public function exportPdf(Request $request)
    {
        // Use DomPDF
        $query = \App\Models\Activity::query();

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
        // Search (on description and type)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }
        // Paginate to match the current page (default 1)
        $perPage = 50;
        $page = $request->get('page', 1);
        $activities = $query->with('user')->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        // Build HTML for PDF
        $html = view('admin.system-settings.audit-logs-pdf', [
            'activities' => $activities,
            'page' => $page,
            'filters' => $request->all(),
        ])->render();

        // Generate PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($html);
        $filename = 'audit_logs_page_' . $page . '_' . date('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export audit logs as Excel
     */
    public function exportExcel(Request $request)
    {
        $logType = $request->get('type', 'all');
        $dateRange = $request->get('date_range', '30');
        
        // Generate Excel report based on type
        // This would typically use a library like PhpSpreadsheet
        
        return response()->json(['message' => 'Excel export functionality would be implemented here']);
    }

    /**
     * Export audit logs as CSV (matching the current page and filters)
     */
    public function exportCsv(Request $request)
    {
        $query = \App\Models\Activity::query();

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

        // Search (on description and type)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Paginate to match the current page (default 1)
        $perPage = 50;
        $page = $request->get('page', 1);
        $activities = $query->with('user')->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        // CSV headers
        $csv = "User,Email,Type,Description,Timestamp\n";
        foreach ($activities as $log) {
            $user = $log->user ? $log->user->name : 'System';
            $email = $log->user ? $log->user->email : '';
            $type = ucfirst(str_replace('_', ' ', $log->type));
            $desc = str_replace(["\n", "\r", '"'], [' ', ' ', '""'], $log->description);
            $timestamp = $log->created_at->format('Y-m-d H:i:s');
            $csv .= '"' . $user . '","' . $email . '","' . $type . '","' . $desc . '","' . $timestamp . "\n";
        }

        $filename = 'audit_logs_page_' . $page . '_' . date('Ymd_His') . '.csv';
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get real-time audit log stream
     */
    public function realTimeLogs()
    {
        // This would typically use WebSockets or Server-Sent Events
        // For now, return recent logs
        $logs = Activity::orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json($logs);
    }

    /**
     * Clear old audit logs
     */
    public function clearOldLogs(Request $request)
    {
        $daysToKeep = $request->get('days', 90);
        $cutoffDate = Carbon::now()->subDays($daysToKeep);

        try {
            $deletedCount = Activity::where('created_at', '<', $cutoffDate)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully cleared {$deletedCount} old audit logs",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear old audit logs'
            ], 500);
        }
    }
} 