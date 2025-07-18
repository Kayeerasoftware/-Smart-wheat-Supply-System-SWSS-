<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FarmerInventory;
use App\Models\Product;

class FarmerInventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:farmer']);
    }

    public function index()
    {
        $user = Auth::user();
        $inventory = FarmerInventory::where('farmer_id', $user->id)
            ->with('product')
            ->get();
        
        return view('farmer.inventory.index', compact('inventory'));
    }

    public function create()
    {
        $wheatProducts = Product::where('is_raw_material', true)
            ->where('name', 'like', '%wheat%')
            ->get();
        
        return view('farmer.inventory.create', compact('wheatProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'quality_grade' => 'required|in:A,B,C',
            'harvest_date' => 'required|date',
            'moisture_content' => 'required|numeric|min:0|max:100',
            'protein_content' => 'required|numeric|min:0|max:100',
            'price_per_kg' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $user = Auth::user();
        
        FarmerInventory::create([
            'farmer_id' => $user->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'quality_grade' => $request->quality_grade,
            'harvest_date' => $request->harvest_date,
            'moisture_content' => $request->moisture_content,
            'protein_content' => $request->protein_content,
            'price_per_kg' => $request->price_per_kg,
            'location' => $request->location,
            'notes' => $request->notes,
            'is_available' => true
        ]);

        return redirect()->route('farmer.inventory.index')
            ->with('success', 'Wheat inventory updated successfully!');
    }

    public function edit(FarmerInventory $inventory)
    {
        $this->authorize('update', $inventory);
        
        $wheatProducts = Product::where('is_raw_material', true)
            ->where('name', 'like', '%wheat%')
            ->get();
        
        return view('farmer.inventory.edit', compact('inventory', 'wheatProducts'));
    }

    public function update(Request $request, FarmerInventory $inventory)
    {
        $this->authorize('update', $inventory);
        
        $request->validate([
            'quantity' => 'required|numeric|min:0',
            'quality_grade' => 'required|in:A,B,C',
            'price_per_kg' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        $inventory->update($request->all());

        return redirect()->route('farmer.inventory.index')
            ->with('success', 'Inventory updated successfully!');
    }
} 