<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of products with search and filtering
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventories.warehouse']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by product type
        if ($request->filled('type')) {
            if ($request->type === 'raw_material') {
                $query->where('is_raw_material', true);
            } elseif ($request->type === 'finished_good') {
                $query->where('is_finished_good', true);
            }
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(15);
        $categories = Category::active()->get();

        // Get low stock alerts
        $lowStockProducts = Product::with('inventories.warehouse')
            ->whereHas('inventories', function ($q) {
                $q->whereRaw('inventories.quantity_available <= products.reorder_point');
            })
            ->get();

        return view('products.index', compact('products', 'categories', 'lowStockProducts'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = Category::active()->get();
        $warehouses = Warehouse::active()->get();
        
        return view('products.create', compact('categories', 'warehouses'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'reorder_point' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:1',
            'supplier_id' => 'nullable|string|max:255',
            'manufacturer_id' => 'nullable|string|max:255',
            'specifications' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_raw_material' => 'boolean',
            'is_finished_good' => 'boolean',
        ]);

        try {
            // Generate SKU
            $category = Category::find($request->category_id);
            $sku = Product::generateSku($category->slug, $request->brand);

            // Handle image uploads
            $images = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $images[] = $path;
                }
            }

            // Create product
            $product = Product::create([
                'sku' => $sku,
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'brand' => $request->brand,
                'unit_of_measure' => $request->unit_of_measure,
                'unit_price' => $request->unit_price,
                'cost_price' => $request->cost_price,
                'reorder_point' => $request->reorder_point,
                'reorder_quantity' => $request->reorder_quantity,
                'supplier_id' => $request->supplier_id,
                'manufacturer_id' => $request->manufacturer_id,
                'specifications' => $request->specifications,
                'images' => $images,
                'is_raw_material' => $request->boolean('is_raw_material'),
                'is_finished_good' => $request->boolean('is_finished_good'),
            ]);

            // Create initial inventory records for all warehouses
            $warehouses = Warehouse::active()->get();
            foreach ($warehouses as $warehouse) {
                Inventory::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'quantity_on_hand' => 0,
                    'quantity_reserved' => 0,
                    'quantity_available' => 0,
                    'quantity_on_order' => 0,
                ]);
            }

            Log::info('Product created successfully', ['product_id' => $product->id, 'sku' => $sku]);

            return redirect()->route('products.index')
                ->with('success', 'Product created successfully!');

        } catch (\Exception $e) {
            Log::error('Product creation failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load(['category', 'inventories.warehouse']);
        
        // Get inventory summary
        $inventorySummary = [
            'total_on_hand' => $product->total_quantity,
            'total_available' => $product->total_available,
            'total_reserved' => $product->total_reserved,
            'total_value' => $product->inventories->sum('value'),
            'low_stock_alerts' => $product->getLowStockAlerts(),
        ];

        // Get recent inventory movements (you'll need to create this table later)
        $recentMovements = []; // Placeholder for inventory movements

        return view('products.show', compact('product', 'inventorySummary', 'recentMovements'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        $warehouses = Warehouse::active()->get();
        
        return view('products.edit', compact('product', 'categories', 'warehouses'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'reorder_point' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:1',
            'supplier_id' => 'nullable|string|max:255',
            'manufacturer_id' => 'nullable|string|max:255',
            'specifications' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_raw_material' => 'boolean',
            'is_finished_good' => 'boolean',
        ]);

        try {
            // Handle image uploads
            $images = $product->images ?? [];
            if ($request->hasFile('images')) {
                // Delete old images
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
                
                // Upload new images
                $images = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $images[] = $path;
                }
            }

            // Update product
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'brand' => $request->brand,
                'unit_of_measure' => $request->unit_of_measure,
                'unit_price' => $request->unit_price,
                'cost_price' => $request->cost_price,
                'reorder_point' => $request->reorder_point,
                'reorder_quantity' => $request->reorder_quantity,
                'supplier_id' => $request->supplier_id,
                'manufacturer_id' => $request->manufacturer_id,
                'specifications' => $request->specifications,
                'images' => $images,
                'is_raw_material' => $request->boolean('is_raw_material'),
                'is_finished_good' => $request->boolean('is_finished_good'),
            ]);

            Log::info('Product updated successfully', ['product_id' => $product->id]);

            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            Log::error('Product update failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        try {
            // Check if product has inventory
            if ($product->total_quantity > 0) {
                return back()->withErrors(['error' => 'Cannot delete product with existing inventory.']);
            }

            // Delete product images
            if ($product->images) {
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $product->delete();

            Log::info('Product deleted successfully', ['product_id' => $product->id]);

            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Product deletion failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to delete product: ' . $e->getMessage()]);
        }
    }

    /**
     * Get low stock alerts
     */
    public function lowStockAlerts()
    {
        $lowStockProducts = Product::with(['category', 'inventories.warehouse'])
            ->whereHas('inventories', function ($q) {
                $q->whereRaw('inventories.quantity_available <= products.reorder_point');
            })
            ->get();

        return view('products.low-stock-alerts', compact('lowStockProducts'));
    }

    /**
     * Get product analytics
     */
    public function analytics()
    {
        $totalProducts = Product::count();
        $activeProducts = Product::active()->count();
        $rawMaterials = Product::rawMaterials()->count();
        $finishedGoods = Product::finishedGoods()->count();
        
        $lowStockCount = Product::whereHas('inventories', function ($q) {
            $q->whereRaw('inventories.quantity_available <= products.reorder_point');
        })->count();

        $totalInventoryValue = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->sum(\DB::raw('inventories.quantity_on_hand * products.cost_price'));

        $analytics = [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'raw_materials' => $rawMaterials,
            'finished_goods' => $finishedGoods,
            'low_stock_count' => $lowStockCount,
            'total_inventory_value' => $totalInventoryValue,
        ];

        return view('products.analytics', compact('analytics'));
    }
}
