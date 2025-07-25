<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of warehouses
     */
    public function index()
    {
        $warehouses = Warehouse::withCount('inventories')
            ->withSum('inventories', 'quantity_on_hand')
            ->orderBy('name')
            ->paginate(15);

        return view('warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new warehouse
     */
    public function create()
    {
        return view('warehouses.create');
    }

    /**
     * Store a newly created warehouse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $warehouse = Warehouse::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'phone' => $request->phone,
                'email' => $request->email,
                'manager_name' => $request->manager_name,
                'capacity' => $request->capacity,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Create inventory records for all existing products
            $products = Product::all();
            foreach ($products as $product) {
                Inventory::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'quantity_on_hand' => 0,
                    'quantity_reserved' => 0,
                    'quantity_available' => 0,
                    'quantity_on_order' => 0,
                ]);
            }

            Log::info('Warehouse created successfully', ['warehouse_id' => $warehouse->id]);

            return redirect()->route('warehouses.index')
                ->with('success', 'Warehouse created successfully!');

        } catch (\Exception $e) {
            Log::error('Warehouse creation failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create warehouse: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified warehouse
     */
    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['inventories.product.category']);
        
        $inventories = $warehouse->inventories()
            ->with(['product.category'])
            ->paginate(20);

        $warehouseStats = [
            'total_products' => $warehouse->inventories()->count(),
            'products_with_stock' => $warehouse->inventories()->where('quantity_on_hand', '>', 0)->count(),
            'total_quantity' => $warehouse->inventories()->sum('quantity_on_hand'),
            'total_value' => $warehouse->inventories()->join('products', 'inventories.product_id', '=', 'products.id')
                ->sum(\DB::raw('inventories.quantity_on_hand * products.cost_price')),
            'low_stock_items' => $warehouse->inventories()
                ->join('products', 'inventories.product_id', '=', 'products.id')
                ->whereRaw('inventories.quantity_available <= products.reorder_point')
                ->count(),
        ];

        return view('warehouses.show', compact('warehouse', 'inventories', 'warehouseStats'));
    }

    /**
     * Show the form for editing the specified warehouse
     */
    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified warehouse
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'description' => 'nullable|string',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $warehouse->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'phone' => $request->phone,
                'email' => $request->email,
                'manager_name' => $request->manager_name,
                'capacity' => $request->capacity,
                'is_active' => $request->boolean('is_active', true),
            ]);

            Log::info('Warehouse updated successfully', ['warehouse_id' => $warehouse->id]);

            return redirect()->route('warehouses.index')
                ->with('success', 'Warehouse updated successfully!');

        } catch (\Exception $e) {
            Log::error('Warehouse update failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to update warehouse: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified warehouse
     */
    public function destroy(Warehouse $warehouse)
    {
        try {
            // Check if warehouse has inventory
            if ($warehouse->inventories()->where('quantity_on_hand', '>', 0)->count() > 0) {
                return back()->withErrors(['error' => 'Cannot delete warehouse with existing inventory.']);
            }

            $warehouse->delete();

            Log::info('Warehouse deleted successfully', ['warehouse_id' => $warehouse->id]);

            return redirect()->route('warehouses.index')
                ->with('success', 'Warehouse deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Warehouse deletion failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to delete warehouse: ' . $e->getMessage()]);
        }
    }

    /**
     * Get warehouse analytics
     */
    public function analytics()
    {
        $totalWarehouses = Warehouse::count();
        $activeWarehouses = Warehouse::active()->count();
        
        $warehouseUtilization = Warehouse::withSum('inventories', 'quantity_on_hand')
            ->with('inventories')
            ->get()
            ->map(function ($warehouse) {
                $totalCapacity = $warehouse->capacity ?? 0;
                $utilization = $totalCapacity > 0 ? ($warehouse->inventories_sum_quantity_on_hand / $totalCapacity) * 100 : 0;
                
                return [
                    'warehouse' => $warehouse,
                    'utilization' => round($utilization, 2),
                ];
            })
            ->sortByDesc('utilization');

        $topWarehouses = Warehouse::withSum('inventories', 'quantity_on_hand')
            ->orderBy('inventories_sum_quantity_on_hand', 'desc')
            ->limit(10)
            ->get();

        $analytics = [
            'total_warehouses' => $totalWarehouses,
            'active_warehouses' => $activeWarehouses,
            'warehouse_utilization' => $warehouseUtilization,
            'top_warehouses' => $topWarehouses,
        ];

        return view('warehouses.analytics', compact('analytics'));
    }

    /**
     * Get warehouse inventory report
     */
    public function inventoryReport(Warehouse $warehouse)
    {
        $inventories = $warehouse->inventories()
            ->with(['product.category'])
            ->get();

        $report = [
            'warehouse' => $warehouse,
            'total_products' => $inventories->count(),
            'products_with_stock' => $inventories->where('quantity_on_hand', '>', 0)->count(),
            'total_quantity' => $inventories->sum('quantity_on_hand'),
            'total_value' => $inventories->sum(function ($inventory) {
                return $inventory->quantity_on_hand * $inventory->product->cost_price;
            }),
            'low_stock_items' => $inventories->filter(function ($inventory) {
                return $inventory->quantity_available <= $inventory->product->reorder_point;
            }),
            'inventories' => $inventories,
        ];

        return view('warehouses.inventory-report', compact('report'));
    }
}
