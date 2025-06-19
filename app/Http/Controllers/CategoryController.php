<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->withSum('products', 'unit_price')
            ->orderBy('name')
            ->paginate(20);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        try {
            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'is_active' => $request->boolean('is_active', true),
            ]);

            Log::info('Category created successfully', ['category_id' => $category->id]);

            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully!');

        } catch (\Exception $e) {
            Log::error('Category creation failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create category: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load(['products', 'children', 'parent']);
        
        $products = $category->products()->with('inventories.warehouse')->paginate(15);
        
        $categoryStats = [
            'total_products' => $category->products()->count(),
            'active_products' => $category->products()->active()->count(),
            'total_value' => $category->products()->join('inventories', 'products.id', '=', 'inventories.product_id')
                ->sum(\DB::raw('inventories.quantity_on_hand * products.cost_price')),
            'subcategories' => $category->children()->count(),
        ];

        return view('categories.show', compact('category', 'products', 'categoryStats'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();
            
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        try {
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'is_active' => $request->boolean('is_active', true),
            ]);

            Log::info('Category updated successfully', ['category_id' => $category->id]);

            return redirect()->route('categories.index')
                ->with('success', 'Category updated successfully!');

        } catch (\Exception $e) {
            Log::error('Category update failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to update category: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        try {
            // Check if category has products
            if ($category->products()->count() > 0) {
                return back()->withErrors(['error' => 'Cannot delete category with existing products.']);
            }

            // Check if category has subcategories
            if ($category->children()->count() > 0) {
                return back()->withErrors(['error' => 'Cannot delete category with existing subcategories.']);
            }

            $category->delete();

            Log::info('Category deleted successfully', ['category_id' => $category->id]);

            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Category deletion failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to delete category: ' . $e->getMessage()]);
        }
    }

    /**
     * Get category tree for dropdowns
     */
    public function tree()
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Get category analytics
     */
    public function analytics()
    {
        $totalCategories = Category::count();
        $activeCategories = Category::active()->count();
        $categoriesWithProducts = Category::has('products')->count();
        
        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(10)
            ->get();

        $categoryValueDistribution = Category::withSum('products', 'unit_price')
            ->orderBy('products_sum_unit_price', 'desc')
            ->limit(10)
            ->get();

        $analytics = [
            'total_categories' => $totalCategories,
            'active_categories' => $activeCategories,
            'categories_with_products' => $categoriesWithProducts,
            'top_categories' => $topCategories,
            'category_value_distribution' => $categoryValueDistribution,
        ];

        return view('categories.analytics', compact('analytics'));
    }
}
