<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FarmerInventory;
use App\Models\Order;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Activity;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supplier']);
    }

    public function index()
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();
        
        \Log::info("Supplier dashboard access - User: {$user->id}, Vendor status: " . ($vendor ? $vendor->status : 'null') . ", Processing status: " . ($vendor ? $vendor->processing_status : 'null'));
        
        // Check if vendor exists and handle different validation states
        if (!$vendor) {
            return redirect()->route('register')->with('error', 'Please complete your supplier registration first.');
        }

        // Auto-complete overdue visits
        if ($vendor->processing_status === 'pending_visit' || $vendor->processing_status === 'visit_completed') {
            $pendingVisit = $vendor->facilityVisits()
                ->where('status', 'scheduled')
                ->where('scheduled_at', '<', now()->subMinute())
                ->first();
            
            if ($pendingVisit) {
                $pendingVisit->update([
                    'status' => 'completed',
                    'outcome' => 'approved',
                    'completed_at' => now()
                ]);
                
                $vendor->update(['processing_status' => 'approved']);
                \Log::info("Auto-completed overdue visit for supplier {$user->id} in dashboard");
            }
        }

        // Route based on processing status
        switch ($vendor->processing_status) {
            case 'pdf_validation_failed':
                return view('dashboards.supplier-validation-failed', compact('vendor'));
                
            case 'pending_visit':
            case 'visit_completed':
                return view('dashboards.supplier-limited-access', compact('vendor'));
                
            case 'approved':
                \Log::info("Showing full dashboard for supplier {$user->id}");
                return $this->showFullDashboard($user, $vendor);
                
            case 'rejected':
                return redirect()->route('supplier.rejected')->with('error', 'Your application has been rejected.');
                
            default:
                // For pending_review or any other status, show limited access
                return view('dashboards.supplier-limited-access', compact('vendor'));
        }
    }

    private function showFullDashboard($user, $vendor)
    {
        // Calculate inventory metrics
        $totalInventory = Inventory::whereHas('product', function($query) use ($user) {
            $query->where('supplier_id', $user->id);
        })->sum('quantity_on_hand');

        // Calculate inventory value
        $totalInventoryValue = Inventory::with('product')
            ->whereHas('product', function($query) use ($user) {
                $query->where('supplier_id', $user->id);
            })
            ->get()
            ->sum(function($inv) {
                return ($inv->quantity_on_hand ?? 0) * ($inv->product->cost_price ?? 0);
            });

        // Calculate order metrics
        $pendingOrders = Order::where('customer_id', $user->id)
            ->whereIn('status', ['pending', 'processing', 'shipped'])
            ->count();
        $activeOrders = $pendingOrders; // Alias for clarity in the view

        $lowStockItems = Inventory::whereHas('product', function($query) use ($user) {
            $query->where('supplier_id', $user->id);
        })->where('quantity_on_hand', '<=', 10)->count();

        // Get recent activities
        $recentActivity = Activity::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get inventory trends for the last 30 days
        $inventoryTrends = $this->getInventoryTrends($user->id);

        // Calculate performance metrics
        $performanceMetrics = $this->calculatePerformanceMetrics($user->id);
        $orderFulfillmentRate = $performanceMetrics['fulfillmentRate'] ?? 0;
        $inventoryTurnover = $performanceMetrics['avgInventory'] ?? 0;

        // Customer satisfaction (dummy for now, can be replaced with real reviews logic)
        $customerSatisfaction = 0.5;
        $customerSatisfactionCount = 0;

        // Get supplier insights
        $supplierInsights = $this->getSupplierInsights($user->id);

        // Load demand forecast and recommendations
        $demandForecast = $this->loadDemandForecast($user->id);
        $forecastRecommendations = $this->loadForecastRecommendations($user->id);

        return view('dashboards.supplier', compact(
            'totalInventory',
            'totalInventoryValue',
            'pendingOrders',
            'activeOrders',
            'lowStockItems',
            'recentActivity',
            'inventoryTrends',
            'performanceMetrics',
            'orderFulfillmentRate',
            'inventoryTurnover',
            'customerSatisfaction',
            'customerSatisfactionCount',
            'supplierInsights',
            'vendor',
            'demandForecast',
            'forecastRecommendations'
        ));
    }

    private function getInventoryTrends($userId)
    {
        $trends = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $total = Inventory::whereHas('product', function($query) use ($userId) {
                $query->where('supplier_id', $userId);
            })->sum('quantity_on_hand');
            
            $trends[] = [
                'date' => $date->format('M d'),
                'total' => $total
            ];
        }
        return $trends;
    }

    private function calculatePerformanceMetrics($userId)
    {
        // Total orders
        $totalOrders = Order::where('customer_id', $userId)->count();
        
        // Completed orders
        $completedOrders = Order::where('customer_id', $userId)
            ->where('status', 'completed')
            ->count();
        
        // Order fulfillment rate
        $fulfillmentRate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;
        
        // Total quantity sold this year
        $totalQuantitySold = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $userId)
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear()
            ])
            ->sum('order_items.quantity');
        
        // Average inventory level
        $avgInventory = Inventory::whereHas('product', function($query) use ($userId) {
            $query->where('supplier_id', $userId);
        })->avg('quantity_on_hand');
        
        return [
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'fulfillmentRate' => round($fulfillmentRate, 1),
            'totalQuantitySold' => $totalQuantitySold,
            'avgInventory' => round($avgInventory, 0)
        ];
    }

    private function getSupplierInsights($userId)
    {
        // Find products and quantities this supplier frequently buys from farmers
        $productPurchases = Order::where('customer_id', $userId)
            ->where('order_type', 'purchase')
            ->with(['orderItems.product'])
            ->get()
            ->flatMap(function($order) {
                return $order->orderItems;
            })
            ->groupBy('product_id')
            ->map(function($items) {
                return [
                    'product' => $items->first()->product,
                    'total_quantity' => $items->sum('quantity'),
                    'frequency' => $items->count()
                ];
            })
            ->sortByDesc('total_quantity')
            ->take(5);

        // If no purchase history, show available wheat products from farmers
        if ($productPurchases->isEmpty()) {
            $wheatProducts = DB::table('farmer_inventories')
                ->join('products', 'farmer_inventories.product_id', '=', 'products.id')
                ->join('users', 'farmer_inventories.farmer_id', '=', 'users.id')
                ->where('farmer_inventories.is_available', true)
                ->where('farmer_inventories.quantity', '>', 0)
                ->where('products.name', 'like', '%wheat%')
                ->select(
                    'products.name as product_name',
                    'users.username as farmer_name',
                    'farmer_inventories.quantity',
                    'farmer_inventories.price_per_kg',
                    'farmer_inventories.quality_grade'
                )
                ->orderByRaw('RANDOM()')
                ->limit(10)
                ->get();

            return [
                'type' => 'available_products',
                'data' => $wheatProducts
            ];
        }

        return [
            'type' => 'purchase_history',
            'data' => $productPurchases
        ];
    }

    public function createOrderFromInsight(Request $request)
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();
        
        if (!$vendor || $vendor->processing_status !== 'approved') {
            return redirect()->back()->with('error', 'You need full access to create orders.');
        }

        // Validate the request
        $request->validate([
            'farmer_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = $request->quantity * $request->unit_price;
            $tax_amount = $subtotal * 0.1; // 10% tax
            $shipping_amount = 50; // Fixed shipping cost
            $total_amount = $subtotal + $tax_amount + $shipping_amount;

            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'customer_id' => $user->id,
                'vendor_id' => $request->farmer_id,
                'order_type' => 'purchase',
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,
                'shipping_amount' => $shipping_amount,
                'total_amount' => $total_amount,
                'order_date' => now(),
                'expected_delivery_date' => now()->addDays(7),
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending',
            ]);

            // Create order item
            DB::table('order_items')->insert([
                'order_id' => $order->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'total_price' => $subtotal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create order. Please try again.');
        }
    }

    public function resubmitApplication(Request $request)
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();
        
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor record not found.');
        }

        $request->validate([
            'application_pdf' => 'required|file|mimes:pdf|max:2048',
            'business_image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:8192',
        ]);

        try {
            $pdfPaths = $vendor->pdf_paths ?? [];
            $imagePaths = $vendor->image_paths ?? [];
            $pdfValidationResult = null;

            // Handle PDF upload
            if ($request->hasFile('application_pdf')) {
                $pdfPath = $request->file('application_pdf')->store('supplier_docs', 'public');
                $pdfPaths['application_pdf'] = $pdfPath;
                
                // Validate PDF with Java server
                try {
                    $fullPdfPath = storage_path('app/public/' . $pdfPath);
                    $javaServerService = app(\App\Services\JavaServerService::class);
                    $pdfValidationResult = $javaServerService->validateSupplierPdf(
                        $fullPdfPath,
                        $user->id,
                        $vendor->application_data['business_name'] ?? 'Unknown'
                    );
                    
                    Log::info('PDF revalidation completed for supplier resubmission', [
                        'user_id' => $user->id,
                        'valid' => $pdfValidationResult['valid'],
                        'score' => $pdfValidationResult['overallScore'] ?? 0
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('PDF revalidation failed during resubmission', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    
                    $pdfValidationResult = [
                        'valid' => false,
                        'message' => 'PDF validation service unavailable: ' . $e->getMessage(),
                        'overallScore' => 0
                    ];
                }
            }

            // Handle image upload
            if ($request->hasFile('business_image')) {
                $imagePath = $request->file('business_image')->store('supplier_images', 'public');
                $imagePaths['business_image'] = $imagePath;
            }

            // Determine new status based on PDF validation
            $newStatus = 'pending';
            $newProcessingStatus = 'pending_review';
            
            if ($pdfValidationResult && $pdfValidationResult['valid']) {
                $newStatus = 'pdf_validated';
                $newProcessingStatus = 'pending_visit';
            } elseif ($pdfValidationResult && !$pdfValidationResult['valid']) {
                $newStatus = 'pdf_rejected';
                $newProcessingStatus = 'pdf_validation_failed';
            }

            // Update vendor record
            $vendor->update([
                'pdf_paths' => $pdfPaths,
                'image_paths' => $imagePaths,
                'status' => $newStatus,
                'processing_status' => $newProcessingStatus,
                'pdf_validation_result' => $pdfValidationResult,
                'score_financial' => $pdfValidationResult['sectionScores']['Financial Stability'] ?? 0,
                'score_reputation' => $pdfValidationResult['sectionScores']['Business Reputation'] ?? 0,
                'score_compliance' => $pdfValidationResult['sectionScores']['Regulatory Compliance'] ?? 0,
                'total_score' => $pdfValidationResult['overallScore'] ?? 0,
            ]);

            Log::info('Supplier application resubmitted', [
                'user_id' => $user->id,
                'new_status' => $newStatus,
                'pdf_valid' => $pdfValidationResult['valid'] ?? false
            ]);

            // Redirect based on validation result
            if ($newProcessingStatus === 'pdf_validation_failed') {
                return redirect()->route('supplier.validation-failed')
                    ->with('error', 'PDF validation failed. Please check the requirements and try again.');
            } elseif ($newProcessingStatus === 'pending_visit') {
                return redirect()->route('supplier.limited-access')
                    ->with('success', 'PDF validation passed! Your application is now pending facility visit.');
            } else {
                return redirect()->route('supplier.limited-access')
                    ->with('info', 'Application submitted successfully. We will review your documents.');
            }

        } catch (\Exception $e) {
            Log::error('Resubmission failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to resubmit application. Please try again.');
        }
    }

    private function loadDemandForecast($userId)
    {
        $forecastPath = storage_path("app/public/forecasts/supplier_{$userId}_demand_forecast.json");
        
        if (file_exists($forecastPath)) {
            $forecastData = json_decode(file_get_contents($forecastPath), true);
            return $forecastData;
        }
        
        return null;
    }

    private function loadForecastRecommendations($userId)
    {
        $recommendationsPath = storage_path("app/public/forecasts/supplier_{$userId}_recommendations.json");
        
        if (file_exists($recommendationsPath)) {
            $recommendations = json_decode(file_get_contents($recommendationsPath), true);
            return $recommendations;
        }
        
        return [];
    }

    public function generateForecast()
    {
        $user = Auth::user();
        
        // Run the Python ML script
        $scriptPath = base_path('ml_scripts/targeted_demand_forecast.py');
        $command = "python \"{$scriptPath}\" supplier {$user->id}";
        
        try {
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                return redirect()->route('supplier.dashboard')
                    ->with('success', 'Demand forecast generated successfully!');
            } else {
                return redirect()->route('supplier.dashboard')
                    ->with('error', 'Failed to generate forecast. Please try again.');
            }
        } catch (\Exception $e) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Error generating forecast: ' . $e->getMessage());
        }
    }

    private function loadCustomerSegments($userId)
    {
        $segmentsPath = storage_path("app/public/forecasts/supplier_{$userId}_customer_segments.json");
        
        if (file_exists($segmentsPath)) {
            $segmentsData = json_decode(file_get_contents($segmentsPath), true);
            return $segmentsData;
        }
        
        return null;
    }

    private function loadSegmentRecommendations($userId)
    {
        $recommendationsPath = storage_path("app/public/forecasts/supplier_{$userId}_segment_recommendations.json");
        
        if (file_exists($recommendationsPath)) {
            $recommendations = json_decode(file_get_contents($recommendationsPath), true);
            return $recommendations;
        }
        
        return [];
    }

    public function runCustomerSegmentation()
    {
        $user = Auth::user();
        
        // Run the Python ML script
        $scriptPath = base_path('ml_scripts/customer_segmentation.py');
        $command = "python \"{$scriptPath}\" supplier {$user->id}";
        
        try {
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                return redirect()->route('supplier.dashboard')
                    ->with('success', 'Customer segmentation completed successfully!');
            } else {
                return redirect()->route('supplier.dashboard')
                    ->with('error', 'Failed to run customer segmentation. Please try again.');
            }
        } catch (\Exception $e) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Error running customer segmentation: ' . $e->getMessage());
        }
    }

    private function getPurchaseHistory($user)
    {
        return Order::where('customer_id', $user->id)
            ->with('orderItems.product')
            ->get()
            ->groupBy(function($order) {
                return $order->orderItems->first()->product->name ?? 'Unknown';
            });
    }

    private function calculateSupplierDiscounts($user, $purchaseHistory)
    {
        $discounts = [];
        
        // Calculate total purchases from each farmer
        $farmerPurchases = [];
        foreach ($purchaseHistory as $productName => $orders) {
            foreach ($orders as $order) {
                $farmerId = $order->orderItems->first()->product->supplier_id ?? null;
                if ($farmerId) {
                    if (!isset($farmerPurchases[$farmerId])) {
                        $farmerPurchases[$farmerId] = [
                            'total_quantity' => 0,
                            'total_amount' => 0,
                            'purchase_count' => 0
                        ];
                    }
                    $farmerPurchases[$farmerId]['total_quantity'] += $order->orderItems->sum('quantity');
                    $farmerPurchases[$farmerId]['total_amount'] += $order->total_amount;
                    $farmerPurchases[$farmerId]['purchase_count']++;
                }
            }
        }
        
        // Generate discounts based on purchase behavior
        foreach ($farmerPurchases as $farmerId => $purchases) {
            $farmer = User::find($farmerId);
            if (!$farmer) continue;
            
            $discounts[$farmerId] = [
                'farmer_name' => $farmer->username,
                'total_purchases' => $purchases['total_amount'],
                'purchase_count' => $purchases['purchase_count'],
                'offers' => []
            ];
            
            // Bulk purchase discount (buy 1000+ kg, get 10% off)
            if ($purchases['total_quantity'] >= 1000) {
                $discounts[$farmerId]['offers'][] = [
                    'type' => 'bulk_purchase',
                    'description' => 'Bulk Purchase Discount (10% off)',
                    'discount_percentage' => 10,
                    'requirement' => 'Purchase 1000+ kg total'
                ];
            }
            
            // Frequent buyer discount (5+ purchases, get 5% off)
            if ($purchases['purchase_count'] >= 5) {
                $discounts[$farmerId]['offers'][] = [
                    'type' => 'frequent_buyer',
                    'description' => 'Frequent Buyer Discount (5% off)',
                    'discount_percentage' => 5,
                    'requirement' => '5+ purchases from this farmer'
                ];
            }
            
            // High value customer discount ($5000+ total, get 15% off)
            if ($purchases['total_amount'] >= 5000) {
                $discounts[$farmerId]['offers'][] = [
                    'type' => 'high_value',
                    'description' => 'High Value Customer Discount (15% off)',
                    'discount_percentage' => 15,
                    'requirement' => '$5000+ total purchases'
                ];
            }
        }
        
        return $discounts;
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'farmer_inventory_id' => 'required|exists:farmer_inventories,id',
            'quantity' => 'required|numeric|min:1',
            'discount_type' => 'nullable|string'
        ]);
        
        $inventory = FarmerInventory::findOrFail($request->farmer_inventory_id);
        
        if ($inventory->quantity < $request->quantity) {
            return back()->with('error', 'Insufficient wheat available.');
        }
        
        // Calculate price with discount
        $basePrice = $inventory->price_per_kg * $request->quantity;
        $discountPercentage = 0;
        
        if ($request->discount_type) {
            $discountPercentage = $this->getDiscountPercentage($request->discount_type);
        }
        
        $discountAmount = ($basePrice * $discountPercentage) / 100;
        $finalPrice = $basePrice - $discountAmount;
        
        // Create order
        $order = Order::create([
            'customer_id' => Auth::id(),
            'vendor_id' => $inventory->farmer_id,
                            'order_type' => 'purchase',
            'status' => 'pending',
            'total_amount' => $finalPrice,
            'discount_amount' => $discountAmount,
            'order_date' => now()
        ]);
        
        // Update inventory
        $inventory->decrement('quantity', $request->quantity);
        if ($inventory->quantity <= 0) {
            $inventory->update(['is_available' => false]);
        }
        
        return redirect()->route('supplier.dashboard')
            ->with('success', "Order placed successfully! Saved: $" . number_format($discountAmount, 2));
    }

    private function getDiscountPercentage($discountType)
    {
        $discounts = [
            'bulk_purchase' => 10,
            'frequent_buyer' => 5,
            'high_value' => 15
        ];
        
        return $discounts[$discountType] ?? 0;
    }

    public function downloadProgressReport()
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor record not found.');
        }

        $pdf = Pdf::loadView('pdf.supplier-progress-report', compact('vendor'));
        return $pdf->download('supplier_progress_report.pdf');
    }

    public function storeContactSupport(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'contact_method' => 'required|in:email,phone'
        ]);

        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        // Log the support request
        Log::info('Support request submitted', [
            'user_id' => $user->id,
            'vendor_id' => $vendor->id ?? null,
            'subject' => $request->subject,
            'contact_method' => $request->contact_method,
            'message' => $request->message
        ]);

        // In a real application, you would:
        // 1. Send email to support team
        // 2. Create a support ticket in the database
        // 3. Send confirmation email to user
        // 4. Store the request in a support_requests table

        return redirect()->back()->with('success', 'Your support request has been submitted successfully. We will contact you via ' . $request->contact_method . ' within 24 hours.');
    }

    public function storeUpdateInfo(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'business_description' => 'required|string|max:1000',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'business_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor information not found.');
        }

        // Update user information
        $user->update([
            'phone' => $request->phone,
            'address' => $request->address
        ]);

        // Update vendor application data
        $applicationData = $vendor->application_data ?? [];
        $applicationData['business_name'] = $request->business_name;
        $applicationData['business_type'] = $request->business_type;
        $applicationData['business_description'] = $request->business_description;

        // Handle business image upload
        if ($request->hasFile('business_image')) {
            $image = $request->file('business_image');
            $imageName = 'business_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('public/business_images', $imageName);
            
            $applicationData['business_image'] = $imagePath;
        }

        $vendor->update([
            'application_data' => $applicationData
        ]);

        // Log the update
        Log::info('Vendor information updated', [
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'updated_fields' => array_keys($request->except(['_token', 'business_image']))
        ]);

        return redirect()->back()->with('success', 'Your information has been updated successfully!');
    }
} 