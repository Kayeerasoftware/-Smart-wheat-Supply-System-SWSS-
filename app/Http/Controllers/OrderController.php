<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();
        
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor profile not found.');
        }
        
        $orders = Order::where('vendor_id', $vendor->id)
            ->with(['orderItems.product', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();
        
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor profile not found.');
        }
        
        $products = Product::where('supplier_id', $user->id)->get();
        $customers = User::where('role', 'retailer')->get();
        
        return view('orders.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'order_type' => 'required|in:purchase,sale,return',
            'expected_delivery_date' => 'required|date|after:today',
            'shipping_address' => 'required|string|max:500',
            'billing_address' => 'required|string|max:500',
            'payment_method' => 'required|in:cash,credit_card,bank_transfer,check',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $vendor = Vendor::where('user_id', $user->id)->first();
            
            if (!$vendor) {
                throw new \Exception('Vendor profile not found.');
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $tax_amount = $subtotal * 0.1; // 10% tax
            $shipping_amount = 50; // Fixed shipping cost
            $discount_amount = 0;
            $total_amount = $subtotal + $tax_amount + $shipping_amount - $discount_amount;

            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_id' => $request->customer_id,
                'vendor_id' => $vendor->id,
                'order_type' => $request->order_type,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,
                'shipping_amount' => $shipping_amount,
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount,
                'notes' => $request->notes,
                'order_date' => now(),
                'expected_delivery_date' => $request->expected_delivery_date,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
            ]);

            // Create order items
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create order. Please try again.');
        }
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'customer', 'vendor']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();
        
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor profile not found.');
        }
        
        $products = Product::where('supplier_id', $user->id)->get();
        $customers = User::where('role', 'retailer')->get();
        $order->load('orderItems.product');
        
        return view('orders.edit', compact('order', 'products', 'customers'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'order_type' => 'required|in:purchase,sale,return',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'expected_delivery_date' => 'required|date',
            'shipping_address' => 'required|string|max:500',
            'billing_address' => 'required|string|max:500',
            'payment_method' => 'required|in:cash,credit_card,bank_transfer,check',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update($request->all());

        return redirect()->route('orders.index')
            ->with('success', 'Order updated successfully!');
    }

    public function destroy(Order $order)
    {
        if ($order->status === 'pending') {
            $order->orderItems()->delete();
            $order->delete();
            return redirect()->route('orders.index')
                ->with('success', 'Order deleted successfully!');
        }

        return back()->with('error', 'Cannot delete order that is not pending.');
    }
} 