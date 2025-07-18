<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerOrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Assuming 'farmer_id' is the field in the orders table for the seller
        $orders = Order::where('farmer_id', $user->id)
            ->with(['orderItems.product', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }
} 