<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;

class DiscountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showDiscounts()
    {
        $user = Auth::user();
        $discounts = $this->getCustomerDiscounts($user);
        
        return view('discounts.index', compact('discounts'));
    }

    public function getCustomerDiscounts($user)
    {
        $discountsPath = storage_path('app/public/discounts/discount_recommendations.json');
        
        if (!file_exists($discountsPath)) {
            return null;
        }
        
        $allDiscounts = json_decode(file_get_contents($discountsPath), true);
        $customerId = $user->role . '_' . $user->id;
        
        return $allDiscounts[$customerId] ?? null;
    }

    public function applyDiscount(Request $request)
    {
        $request->validate([
            'discount_type' => 'required|string',
            'order_id' => 'required|exists:orders,id'
        ]);
        
        $user = Auth::user();
        $discounts = $this->getCustomerDiscounts($user);
        
        if (!$discounts) {
            return back()->with('error', 'No discounts available for your account.');
        }
        
        // Find the specific discount
        $selectedDiscount = null;
        foreach ($discounts['discounts'] as $discount) {
            if ($discount['type'] === $request->discount_type) {
                $selectedDiscount = $discount;
                break;
            }
        }
        
        if (!$selectedDiscount) {
            return back()->with('error', 'Selected discount not available.');
        }
        
        // Apply discount to order (you can extend this logic)
        $order = Order::find($request->order_id);
        $discountAmount = ($order->total_amount * $selectedDiscount['discount_percentage']) / 100;
        $order->discount_amount = $discountAmount;
        $order->total_amount = $order->total_amount - $discountAmount;
        $order->save();
        
        return back()->with('success', "Discount applied! Saved: $" . number_format($discountAmount, 2));
    }

    public function runDiscountCalculation()
    {
        $scriptPath = base_path('ml_scripts/discount_calculator.py');
        $output = shell_exec("python \"$scriptPath\" 2>&1");
        
        return redirect()->back()->with('success', 'Discount calculations completed! ' . $output);
    }
} 