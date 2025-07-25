<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if supplier has access to inventory
     */
    private function checkSupplierAccess()
    {
        $user = Auth::user();
        
        if ($user->role === 'supplier') {
            $vendor = Vendor::where('user_id', $user->id)->first();
            
            // Allow access if vendor has basic access (PDF validated) or full access
            if (!$vendor || (!$vendor->hasBasicAccess() && !$vendor->hasFullAccess())) {
                return redirect()->route('supplier.dashboard')
                    ->with('error', 'You need PDF validation to access inventory management.');
            }
        }
        
        return null; // Access allowed
    }

    /**
     * Display a listing of inventory
     */
    public function index(Request $request)
    {
        // Check supplier access
        $accessCheck = $this->checkSupplierAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        $query = Inventory::with(['product.category', 'warehouse']);

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by stock level
        if ($request->filled('stock_level')) {
            switch ($request->stock_level) {
                case 'low':
                    $query->whereHas('product', function ($q) {
                        $q->whereRaw('inventories.quantity_available <= products.reorder_point');
                    });
                    break;
                case 'out_of_stock':
                    $query->where('quantity_available', 0);
                    break;
                case 'in_stock':
                    $query->where('quantity_available', '>', 0);
                    break;
            }
        }

        // Search by product name or SKU
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $inventories = $query->paginate(20);
        $warehouses = Warehouse::active()->get();
        $products = Product::active()->get();

        return view('inventory.index', compact('inventories', 'warehouses', 'products'));
    }

    /**
     * Show the form for creating a new inventory adjustment
     */
    public function create()
    {
        // Check supplier access
        $accessCheck = $this->checkSupplierAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        try {
            $products = Product::active()->get();
            $warehouses = Warehouse::active()->get();
            
            // Debug information
            \Log::info('Inventory create method called', [
                'products_count' => $products->count(),
                'warehouses_count' => $warehouses->count(),
                'view_path' => resource_path('views/inventory/create.blade.php')
            ]);
            
            return view('inventory.create', compact('products', 'warehouses'));
        } catch (\Exception $e) {
            \Log::error('Error in inventory create method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Store a new inventory adjustment
     */
    public function store(Request $request)
    {
        // Check supplier access
        $accessCheck = $this->checkSupplierAccess();
        if ($accessCheck) {
            return $accessCheck;
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'adjustment_type' => 'required|in:receipt,issue,transfer,adjustment',
            'quantity' => 'required|numeric|min:0.01',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'batch_number' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        try {
            DB::beginTransaction();

            $inventory = Inventory::where('product_id', $request->product_id)
                ->where('warehouse_id', $request->warehouse_id)
                ->first();

            if (!$inventory) {
                $inventory = Inventory::create([
                    'product_id' => $request->product_id,
                    'warehouse_id' => $request->warehouse_id,
                    'quantity_on_hand' => 0,
                    'quantity_reserved' => 0,
                    'quantity_available' => 0,
                    'quantity_on_order' => 0,
                ]);
            }

            $oldQuantity = $inventory->quantity_on_hand;
            $adjustmentQuantity = $request->quantity;

            switch ($request->adjustment_type) {
                case 'receipt':
                    $inventory->quantity_on_hand += $adjustmentQuantity;
                    $inventory->quantity_available += $adjustmentQuantity;
                    break;
                case 'issue':
                    if ($inventory->quantity_available < $adjustmentQuantity) {
                        throw new \Exception('Insufficient stock available for issue.');
                    }
                    $inventory->quantity_on_hand -= $adjustmentQuantity;
                    $inventory->quantity_available -= $adjustmentQuantity;
                    break;
                case 'adjustment':
                    $inventory->quantity_on_hand = $adjustmentQuantity;
                    $inventory->quantity_available = $adjustmentQuantity - $inventory->quantity_reserved;
                    break;
            }

            $inventory->save();

            // Log the inventory movement (you'll need to create this table later)
            // InventoryMovement::create([
            //     'inventory_id' => $inventory->id,
            //     'movement_type' => $request->adjustment_type,
            //     'quantity' => $adjustmentQuantity,
            //     'reference_number' => $request->reference_number,
            //     'notes' => $request->notes,
            //     'batch_number' => $request->batch_number,
            //     'expiry_date' => $request->expiry_date,
            //     'user_id' => auth()->id(),
            // ]);

            DB::commit();

            Log::info('Inventory adjustment created', [
                'inventory_id' => $inventory->id,
                'adjustment_type' => $request->adjustment_type,
                'quantity' => $adjustmentQuantity,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $inventory->quantity_on_hand,
            ]);

            return redirect()->route('inventory.index')
                ->with('success', 'Inventory adjustment created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Inventory adjustment failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create inventory adjustment: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified inventory
     */
    public function show(Inventory $inventory)
    {
        $inventory->load(['product.category', 'warehouse']);
        
        // Get inventory value
        $inventoryValue = $inventory->quantity_on_hand * $inventory->product->cost_price;
        
        // Get stock status
        $stockStatus = $this->getStockStatus($inventory);
        
        // Get recent movements (placeholder)
        $recentMovements = []; // You'll implement this when you create the movements table

        return view('inventory.show', compact('inventory', 'inventoryValue', 'stockStatus', 'recentMovements'));
    }

    /**
     * Show the form for editing inventory
     */
    public function edit(Inventory $inventory)
    {
        $inventory->load(['product', 'warehouse']);
        return view('inventory.edit', compact('inventory'));
    }

    /**
     * Update inventory quantities
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity_on_hand' => 'required|numeric|min:0',
            'quantity_reserved' => 'required|numeric|min:0',
            'quantity_on_order' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $oldQuantities = [
                'on_hand' => $inventory->quantity_on_hand,
                'reserved' => $inventory->quantity_reserved,
                'on_order' => $inventory->quantity_on_order,
            ];

            $inventory->update([
                'quantity_on_hand' => $request->quantity_on_hand,
                'quantity_reserved' => $request->quantity_reserved,
                'quantity_on_order' => $request->quantity_on_order,
                'quantity_available' => $request->quantity_on_hand - $request->quantity_reserved,
            ]);

            Log::info('Inventory updated', [
                'inventory_id' => $inventory->id,
                'old_quantities' => $oldQuantities,
                'new_quantities' => $request->only(['quantity_on_hand', 'quantity_reserved', 'quantity_on_order']),
            ]);

            return redirect()->route('inventory.show', $inventory)
                ->with('success', 'Inventory updated successfully!');

        } catch (\Exception $e) {
            Log::error('Inventory update failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to update inventory: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified inventory
     */
    public function destroy(Inventory $inventory)
    {
        try {
            if ($inventory->quantity_on_hand > 0) {
                return back()->withErrors(['error' => 'Cannot delete inventory with existing stock.']);
            }

            $inventory->delete();

            Log::info('Inventory deleted', ['inventory_id' => $inventory->id]);

            return redirect()->route('inventory.index')
                ->with('success', 'Inventory record deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Inventory deletion failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to delete inventory: ' . $e->getMessage()]);
        }
    }

    /**
     * Get low stock alerts
     */
    public function lowStockAlerts()
    {
        $lowStockItems = Inventory::with(['product.category', 'warehouse'])
            ->whereHas('product', function ($q) {
                $q->whereRaw('inventories.quantity_available <= products.reorder_point');
            })
            ->get();

        return view('inventory.low-stock-alerts', compact('lowStockItems'));
    }

    /**
     * Get inventory analytics
     */
    public function analytics()
    {
        $totalProducts = Inventory::distinct('product_id')->count();
        $totalWarehouses = Inventory::distinct('warehouse_id')->count();
        
        $totalInventoryValue = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->sum(DB::raw('inventories.quantity_on_hand * products.cost_price'));

        $lowStockCount = Inventory::whereHas('product', function ($q) {
            $q->whereRaw('inventories.quantity_available <= products.reorder_point');
        })->count();

        $outOfStockCount = Inventory::where('quantity_available', 0)->count();

        $topProducts = Inventory::with('product')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->select('product_id', DB::raw('SUM(quantity_on_hand) as total_quantity'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        $analytics = [
            'total_products' => $totalProducts,
            'total_warehouses' => $totalWarehouses,
            'total_inventory_value' => $totalInventoryValue,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'top_products' => $topProducts,
        ];

        return view('inventory.analytics', compact('analytics'));
    }

    /**
     * Get stock status for inventory
     */
    private function getStockStatus($inventory)
    {
        if ($inventory->quantity_available <= 0) {
            return 'out_of_stock';
        } elseif ($inventory->quantity_available <= $inventory->product->reorder_point) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Bulk inventory adjustment
     */
    public function bulkAdjustment(Request $request)
    {
        $request->validate([
            'adjustments' => 'required|array',
            'adjustments.*.product_id' => 'required|exists:products,id',
            'adjustments.*.warehouse_id' => 'required|exists:warehouses,id',
            'adjustments.*.quantity' => 'required|numeric|min:0.01',
            'adjustments.*.type' => 'required|in:receipt,issue,adjustment',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->adjustments as $adjustment) {
                $inventory = Inventory::where('product_id', $adjustment['product_id'])
                    ->where('warehouse_id', $adjustment['warehouse_id'])
                    ->first();

                if (!$inventory) {
                    $inventory = Inventory::create([
                        'product_id' => $adjustment['product_id'],
                        'warehouse_id' => $adjustment['warehouse_id'],
                        'quantity_on_hand' => 0,
                        'quantity_reserved' => 0,
                        'quantity_available' => 0,
                        'quantity_on_order' => 0,
                    ]);
                }

                switch ($adjustment['type']) {
                    case 'receipt':
                        $inventory->quantity_on_hand += $adjustment['quantity'];
                        $inventory->quantity_available += $adjustment['quantity'];
                        break;
                    case 'issue':
                        if ($inventory->quantity_available < $adjustment['quantity']) {
                            throw new \Exception("Insufficient stock for product ID {$adjustment['product_id']}");
                        }
                        $inventory->quantity_on_hand -= $adjustment['quantity'];
                        $inventory->quantity_available -= $adjustment['quantity'];
                        break;
                    case 'adjustment':
                        $inventory->quantity_on_hand = $adjustment['quantity'];
                        $inventory->quantity_available = $adjustment['quantity'] - $inventory->quantity_reserved;
                        break;
                }

                $inventory->save();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Bulk adjustment completed successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
