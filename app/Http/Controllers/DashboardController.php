<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Activity;

class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // Get recent activity for the user
        $recentActivity = Activity::where('user_id', $user->id)
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
                return view('dashboards.admin', $data);

            case 'farmer':
                $data['activeCrops'] = 0; // Replace with actual query
                $data['harvestReady'] = 0; // Replace with actual query
                $data['pendingOrders'] = 0; // Replace with actual query
                $data['marketPrice'] = 0.00; // Replace with actual query
                return view('dashboards.farmer', $data);

            case 'supplier':
                $data['totalInventory'] = 0; // Replace with actual query
                $data['activeOrders'] = 0; // Replace with actual query
                $data['lowStockItems'] = 0; // Replace with actual query
                $data['pendingDeliveries'] = 0; // Replace with actual query
                return view('dashboards.supplier', $data);

            case 'manufacturer':
                $data['activeLines'] = 0; // Replace with actual query
                $data['dailyOutput'] = 0; // Replace with actual query
                $data['qualityIssues'] = 0; // Replace with actual query
                $data['rawMaterials'] = 0; // Replace with actual query
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

            default:
                return redirect()->route('login');
        }
    }

    // Individual role methods for direct access
    public function admin()
    {
        return $this->index();
    }

    public function farmer()
    {
        return $this->index();
    }

    public function supplier()
    {
        return $this->index();
    }

    public function manufacturer()
    {
        return $this->index();
    }

    public function distributor()
    {
        return $this->index();
    }

    public function retailer()
    {
        return $this->index();
    }
}