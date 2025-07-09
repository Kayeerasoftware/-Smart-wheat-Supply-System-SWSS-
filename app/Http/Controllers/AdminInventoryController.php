<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminInventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of all inventory (admin view)
     */
    public function index(Request $request)
    {
        $query = Inventory::with(['product.category', 'warehouse', 'product.supplier']);

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
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

        // Search by product name, SKU, or supplier
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                })
                ->orWhereHas('product.supplier', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $inventories = $query->paginate(20);
        $warehouses = Warehouse::active()->get();
        $products = Product::active()->get();
        $suppliers = User::where('role', 'supplier')->get();

        // Get summary statistics
        $summary = [
            'total_items' => Inventory::count(),
            'total_value' => Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                ->sum(DB::raw('inventories.quantity_on_hand * products.cost_price')),
            'low_stock_items' => Inventory::whereHas('product', function ($q) {
                $q->whereRaw('inventories.quantity_available <= products.reorder_point');
            })->count(),
            'out_of_stock_items' => Inventory::where('quantity_available', 0)->count(),
            'total_suppliers' => User::where('role', 'supplier')->count(),
        ];

        return view('admin.inventory.index', compact('inventories', 'warehouses', 'products', 'suppliers', 'summary'));
    }

    /**
     * Show the form for creating a new inventory adjustment (admin)
     */
    public function create()
    {
        $products = Product::active()->get();
        $warehouses = Warehouse::active()->get();
        $suppliers = User::where('role', 'supplier')->get();
        
        return view('admin.inventory.create', compact('products', 'warehouses', 'suppliers'));
    }

    /**
     * Store a new inventory adjustment (admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'adjustment_type' => 'required|in:receipt,issue,transfer,adjustment',
            'quantity' => 'required|numeric|min:0.01',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'batch_number' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'admin_notes' => 'nullable|string',
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

            // Log the admin action
            Log::info('Admin inventory adjustment', [
                'admin_id' => Auth::id(),
                'inventory_id' => $inventory->id,
                'adjustment_type' => $request->adjustment_type,
                'quantity' => $adjustmentQuantity,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $inventory->quantity_on_hand,
                'admin_notes' => $request->admin_notes,
            ]);

            DB::commit();

            return redirect()->route('admin.inventory.index')
                ->with('success', 'Inventory adjustment created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin inventory adjustment failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create inventory adjustment: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified inventory (admin view)
     */
    public function show(Inventory $inventory)
    {
        $inventory->load(['product.category', 'warehouse', 'product.supplier']);
        
        // Get inventory value
        $inventoryValue = $inventory->quantity_on_hand * $inventory->product->cost_price;
        
        // Get stock status
        $stockStatus = $this->getStockStatus($inventory);
        
        // Get admin notes and history
        $adminNotes = []; // You can implement this when you create admin notes table

        return view('admin.inventory.show', compact('inventory', 'inventoryValue', 'stockStatus', 'adminNotes'));
    }

    /**
     * Show the form for editing inventory (admin)
     */
    public function edit(Inventory $inventory)
    {
        $inventory->load(['product', 'warehouse']);
        return view('admin.inventory.edit', compact('inventory'));
    }

    /**
     * Update inventory quantities (admin)
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity_on_hand' => 'required|numeric|min:0',
            'quantity_reserved' => 'required|numeric|min:0',
            'quantity_on_order' => 'required|numeric|min:0',
            'admin_notes' => 'nullable|string',
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

            Log::info('Admin inventory update', [
                'admin_id' => Auth::id(),
                'inventory_id' => $inventory->id,
                'old_quantities' => $oldQuantities,
                'new_quantities' => $request->only(['quantity_on_hand', 'quantity_reserved', 'quantity_on_order']),
                'admin_notes' => $request->admin_notes,
            ]);

            return redirect()->route('admin.inventory.show', $inventory)
                ->with('success', 'Inventory updated successfully!');

        } catch (\Exception $e) {
            Log::error('Admin inventory update failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to update inventory: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified inventory (admin)
     */
    public function destroy(Inventory $inventory)
    {
        try {
            if ($inventory->quantity_on_hand > 0) {
                return back()->withErrors(['error' => 'Cannot delete inventory with existing stock.']);
            }

            $inventory->delete();

            Log::info('Admin inventory deletion', [
                'admin_id' => Auth::id(),
                'inventory_id' => $inventory->id,
            ]);

            return redirect()->route('admin.inventory.index')
                ->with('success', 'Inventory record deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Admin inventory deletion failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to delete inventory: ' . $e->getMessage()]);
        }
    }

    /**
     * Get low stock alerts (admin view)
     */
    public function lowStockAlerts()
    {
        $lowStockItems = Inventory::with(['product.category', 'warehouse', 'product.supplier'])
            ->whereHas('product', function ($q) {
                $q->whereRaw('inventories.quantity_available <= products.reorder_point');
            })
            ->get();

        return view('admin.inventory.low-stock-alerts', compact('lowStockItems'));
    }

    /**
     * Get inventory analytics (admin view)
     */
    public function analytics()
    {
        $totalProducts = Inventory::distinct('product_id')->count();
        $totalWarehouses = Inventory::distinct('warehouse_id')->count();
        $totalSuppliers = User::where('role', 'supplier')->count();
        
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

        $supplierInventory = Inventory::with(['product.supplier'])
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->join('users', 'products.supplier_id', '=', 'users.id')
            ->select('users.id', 'users.name', DB::raw('SUM(inventories.quantity_on_hand) as total_quantity'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        $analytics = [
            'total_products' => $totalProducts,
            'total_warehouses' => $totalWarehouses,
            'total_suppliers' => $totalSuppliers,
            'total_inventory_value' => $totalInventoryValue,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'top_products' => $topProducts,
            'supplier_inventory' => $supplierInventory,
        ];

        return view('admin.inventory.analytics', compact('analytics'));
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
} 