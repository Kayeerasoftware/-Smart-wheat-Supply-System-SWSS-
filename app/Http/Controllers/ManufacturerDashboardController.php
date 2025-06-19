<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\ProductionLine;
use App\Models\QualityCheck;
use App\Models\RawMaterial;
use App\Models\ManufacturingOrder;

class ManufacturerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:manufacturer');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get recent activity for the user
        $recentActivity = Activity::where('user_id', $user->user_id)
            ->latest()
            ->take(5)
            ->get();

        // Get active production lines
        $activeLines = 0; // ProductionLine::where('status', 'active')->count();
        
        // Get all production lines with current order
        $productionLines = collect(); // ProductionLine::with('currentOrder')->get();
        
        // Get daily output
        $dailyOutput = 0; // ManufacturingOrder::whereDate('created_at', today())->where('status', 'completed')->sum('quantity');
        
        // Get quality issues
        $qualityIssues = 0; // QualityCheck::where('status', 'failed')->whereDate('created_at', today())->count();
        
        // Get raw materials inventory
        $rawMaterials = 0; // RawMaterial::where('quantity', '>', 0)->sum('quantity');
        
        // Low inventory alert (threshold: 20 units)
        $lowInventoryMaterials = collect(); // RawMaterial::where('quantity', '<', 20)->get();
        
        // Get current and pending orders
        $currentOrders = collect(); // ManufacturingOrder::whereIn('status', ['pending', 'in_progress'])->orderBy('created_at', 'desc')->take(5)->get();
        
        // Recent quality checks
        $recentQualityChecks = collect(); // QualityCheck::with(['productionLine', 'order'])->latest()->take(5)->get();
        
        // Supplier list
        $suppliers = collect(); // \App\Models\Supplier::all();
        
        // Quality trends (last 7 days)
        $qualityTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $passed = 0; // QualityCheck::whereDate('created_at', $date)->where('status', 'passed')->count();
            $failed = 0; // QualityCheck::whereDate('created_at', $date)->where('status', 'failed')->count();
            $qualityTrends[] = [
                'date' => $date->format('Y-m-d'),
                'passed' => $passed,
                'failed' => $failed,
            ];
        }

        $data = [
            'recentActivity' => $recentActivity,
            'activeLines' => $activeLines,
            'dailyOutput' => $dailyOutput,
            'qualityIssues' => $qualityIssues,
            'rawMaterials' => $rawMaterials,
            'productionLines' => $productionLines,
            'currentOrders' => $currentOrders,
            'lowInventoryMaterials' => $lowInventoryMaterials,
            'recentQualityChecks' => $recentQualityChecks,
            'suppliers' => $suppliers,
            'qualityTrends' => $qualityTrends,
        ];

        return view('dashboards.manufacturer', $data);
    }

    public function productionLines()
    {
        $lines = ProductionLine::with(['currentOrder', 'qualityChecks'])
            ->latest()
            ->get();
            
        return view('manufacturer.production-lines.index', compact('lines'));
    }

    public function qualityChecks()
    {
        $checks = QualityCheck::with(['productionLine', 'order'])
            ->latest()
            ->paginate(10);
            
        return view('manufacturer.quality-checks.index', compact('checks'));
    }

    public function rawMaterials()
    {
        $materials = RawMaterial::with(['supplier'])
            ->orderBy('quantity', 'asc')
            ->get();
            
        return view('manufacturer.raw-materials.index', compact('materials'));
    }

    // Production Line Management
    public function createProductionLine()
    {
        return view('manufacturer.production-lines.create');
    }

    public function storeProductionLine(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        ProductionLine::create($validated);

        return redirect()->route('manufacturer.production-lines')
            ->with('success', 'Production line created successfully');
    }

    public function editProductionLine(ProductionLine $productionLine)
    {
        return view('manufacturer.production-lines.edit', compact('productionLine'));
    }

    public function updateProductionLine(Request $request, ProductionLine $productionLine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $productionLine->update($validated);

        return redirect()->route('manufacturer.production-lines')
            ->with('success', 'Production line updated successfully');
    }

    // Quality Check Management
    public function createQualityCheck()
    {
        $productionLines = ProductionLine::where('status', 'active')->get();
        return view('manufacturer.quality-checks.create', compact('productionLines'));
    }

    public function storeQualityCheck(Request $request)
    {
        $validated = $request->validate([
            'production_line_id' => 'required|exists:production_lines,id',
            'order_id' => 'required|exists:manufacturing_orders,id',
            'status' => 'required|in:passed,failed',
            'notes' => 'nullable|string',
        ]);

        QualityCheck::create($validated);

        return redirect()->route('manufacturer.quality-checks')
            ->with('success', 'Quality check created successfully');
    }

    public function editQualityCheck(QualityCheck $qualityCheck)
    {
        $productionLines = ProductionLine::where('status', 'active')->get();
        return view('manufacturer.quality-checks.edit', compact('qualityCheck', 'productionLines'));
    }

    public function updateQualityCheck(Request $request, QualityCheck $qualityCheck)
    {
        $validated = $request->validate([
            'production_line_id' => 'required|exists:production_lines,id',
            'order_id' => 'required|exists:manufacturing_orders,id',
            'status' => 'required|in:passed,failed',
            'notes' => 'nullable|string',
        ]);

        $qualityCheck->update($validated);

        return redirect()->route('manufacturer.quality-checks')
            ->with('success', 'Quality check updated successfully');
    }

    // Raw Material Management
    public function createRawMaterial()
    {
        return view('manufacturer.raw-materials.create');
    }

    public function storeRawMaterial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        RawMaterial::create($validated);

        return redirect()->route('manufacturer.raw-materials')
            ->with('success', 'Raw material added successfully');
    }

    public function editRawMaterial(RawMaterial $rawMaterial)
    {
        return view('manufacturer.raw-materials.edit', compact('rawMaterial'));
    }

    public function updateRawMaterial(Request $request, RawMaterial $rawMaterial)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $rawMaterial->update($validated);

        return redirect()->route('manufacturer.raw-materials')
            ->with('success', 'Raw material updated successfully');
    }

    public function createOrder()
    {
        return view('manufacturer.orders.create');
    }

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:1',
            'unit' => 'required|in:kg,tons,pieces,bags',
            'production_line_id' => 'required|exists:production_lines,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
        ]);

        // Add default status
        $validated['status'] = 'pending';
        $validated['user_id'] = auth()->id();

        ManufacturingOrder::create($validated);

        return redirect()->route('manufacturer.dashboard')
            ->with('success', 'Manufacturing order created successfully');
    }
} 