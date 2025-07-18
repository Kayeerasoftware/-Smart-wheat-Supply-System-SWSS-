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
        $user = auth()->user();
        $role = $user->role;
        $userId = $user->id;
        
        // Load demand forecast if available
        $forecast = [];
        $forecastPath = storage_path("app/public/forecasts/forecast_{$role}_{$userId}.json");
        if (file_exists($forecastPath)) {
            $forecast = json_decode(file_get_contents($forecastPath), true);
        }
        
        // Load customer segments if available
        $customerSegments = [];
        $segmentsPath = storage_path('app/public/segments/customer_segments.json');
        if (file_exists($segmentsPath)) {
            $customerSegments = json_decode(file_get_contents($segmentsPath), true);
        }
        
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

        // Get customer insights for manufacturing decisions
        $customerInsights = $this->getCustomerInsights($customerSegments);
        
        // Get production recommendations based on customer segments
        $productionRecommendations = $this->getProductionRecommendations($customerSegments);

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
            'forecast' => $forecast,
            'customerSegments' => $customerSegments,
            'customerInsights' => $customerInsights,
            'productionRecommendations' => $productionRecommendations,
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

    private function getCustomerInsights($customerSegments)
    {
        $insights = [
            'totalCustomers' => 0,
            'segmentBreakdown' => [],
            'demandPatterns' => [],
        ];

        if (!empty($customerSegments)) {
            $customers = $customerSegments['customers'] ?? [];
            $insights['totalCustomers'] = count($customers);
            $insights['segmentBreakdown'] = $this->getSegmentBreakdown($customers);
            $insights['demandPatterns'] = $this->getDemandPatterns($customers);
        }

        return $insights;
    }

    private function getSegmentBreakdown($customers)
    {
        $breakdown = [];
        foreach ($customers as $customer) {
            $segment = $customer['segment_name'] ?? 'Unknown';
            $breakdown[$segment] = ($breakdown[$segment] ?? 0) + 1;
        }
        return $breakdown;
    }

    private function getDemandPatterns($customers)
    {
        $patterns = [
            'highValueCustomers' => 0,
            'frequentBuyers' => 0,
            'seasonalDemand' => [],
        ];

        foreach ($customers as $customer) {
            $segment = $customer['segment_name'] ?? '';
            $monetary = $customer['monetary'] ?? 0;
            $frequency = $customer['frequency'] ?? 0;

            if ($monetary > 1000) {
                $patterns['highValueCustomers']++;
            }
            if ($frequency > 10) {
                $patterns['frequentBuyers']++;
            }
        }

        return $patterns;
    }

    private function getProductionRecommendations($customerSegments)
    {
        $recommendations = [];

        if (!empty($customerSegments)) {
            $customers = $customerSegments['customers'] ?? [];
            $segmentCounts = [];
            
            foreach ($customers as $customer) {
                $segment = $customer['segment_name'] ?? 'Unknown';
                $segmentCounts[$segment] = ($segmentCounts[$segment] ?? 0) + 1;
            }

            // Production recommendations based on customer segments
            if (isset($segmentCounts['Champions'])) {
                $recommendations[] = "Increase production capacity for premium products (Champions segment)";
            }
            if (isset($segmentCounts['Frequent Customers'])) {
                $recommendations[] = "Optimize production lines for high-volume products";
            }
            if (isset($segmentCounts['Big Spenders'])) {
                $recommendations[] = "Focus on quality control for high-value customers";
            }
            if (isset($segmentCounts['Recent Customers'])) {
                $recommendations[] = "Maintain diverse product range for new customers";
            }
        }

        return $recommendations;
    }

    public function customerSegments()
    {
        $user = auth()->user();
        
        // Load customer segments
        $segments = [];
        $segmentsPath = storage_path('app/public/segments/customer_segments.json');
        if (file_exists($segmentsPath)) {
            $segments = json_decode(file_get_contents($segmentsPath), true);
        }

        return view('manufacturer.customer-segments', compact('segments'));
    }

    public function runSegmentation()
    {
        $scriptPath = base_path('ml_scripts/customer_segmentation.py');
        $output = shell_exec("python \"$scriptPath\" 2>&1");
        
        return redirect()->back()->with('success', 'Customer segmentation completed! ' . $output);
    }

    public function productionInsights()
    {
        $user = auth()->user();
        
        // Load customer segments and production data
        $segments = [];
        $segmentsPath = storage_path('app/public/segments/customer_segments.json');
        if (file_exists($segmentsPath)) {
            $segments = json_decode(file_get_contents($segmentsPath), true);
        }

        return view('manufacturer.production-insights', compact('segments'));
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