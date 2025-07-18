@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-blue-900 py-8 px-4 sm:px-6 lg:px-8 flex flex-col justify-center">
    <div class="w-full max-w-3xl mx-auto">
        <div class="bg-white/5 backdrop-blur-sm rounded-3xl shadow-xl overflow-hidden border border-white/10">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600/30 to-purple-600/30 p-6 border-b border-white/10">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h1 class="text-3xl font-bold text-white tracking-tight">
                        <i class="fas fa-shopping-basket mr-2 text-blue-300"></i> Create New Order
                    </h1>
                    <a href="{{ route('orders.index') }}" class="flex items-center text-blue-300 hover:text-blue-200 transition-colors font-medium">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Orders
                    </a>
                </div>
                <p class="mt-2 text-blue-100 text-sm">Fill in the details below to create a new wheat purchase order</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-500/10 border-l-4 border-red-400 text-red-100 p-4 mx-6 mt-6 rounded-lg animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <h3 class="font-bold">Please fix these issues:</h3>
                    </div>
                    <ul class="list-disc pl-5 mt-2 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Section -->
            <div class="p-6 sm:p-8">
                <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Farmer Selection -->
                    <div class="space-y-2">
                        <label for="customer_id" class="block text-sm font-medium text-blue-100 flex items-center">
                            <i class="fas fa-user-tie mr-2 text-blue-300"></i> Farmer (Wheat Supplier)
                        </label>
                        <select id="customer_id" name="customer_id" class="w-full bg-gray-800/70 text-white border border-gray-600/50 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 shadow-sm" required>
                            <option value="">Select Farmer with Available Wheat</option>
                            @foreach($customers as $customer)
                                @php
                                    $wheatProducts = $customer->farmerInventory->where('is_available', true)->where('quantity', '>', 0);
                                @endphp
                                @if($wheatProducts->count() > 0)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }} class="hover:bg-blue-600">
                                        {{ $customer->name }} ({{ $customer->email }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <p class="text-xs text-blue-200/70">Only farmers with available wheat inventory are shown</p>
                    </div>

                    <!-- Order Type and Delivery Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="order_type" class="block text-sm font-medium text-blue-100 flex items-center">
                                <i class="fas fa-tags mr-2 text-blue-300"></i> Order Type
                            </label>
                            <select id="order_type" name="order_type" class="w-full bg-gray-800/70 text-white border border-gray-600/50 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 shadow-sm" required>
                                <option value="purchase" {{ old('order_type', 'purchase') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                <option value="return" {{ old('order_type') == 'return' ? 'selected' : '' }}>Return</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label for="expected_delivery_date" class="block text-sm font-medium text-blue-100 flex items-center">
                                <i class="far fa-calendar-alt mr-2 text-blue-300"></i> Expected Delivery Date
                            </label>
                            <div class="relative">
                                <input type="date" id="expected_delivery_date" name="expected_delivery_date" class="w-full bg-gray-800/70 text-white border border-gray-600/50 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 shadow-sm" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('expected_delivery_date') }}">
                                <i class="fas fa-calendar-day absolute right-3 top-3 text-blue-300/70"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="shipping_address" class="block text-sm font-medium text-blue-100 flex items-center">
                                <i class="fas fa-truck mr-2 text-blue-300"></i> Shipping Address
                            </label>
                            <textarea id="shipping_address" name="shipping_address" rows="2" class="w-full bg-gray-800/70 text-white border border-gray-600/50 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 shadow-sm" required minlength="10" maxlength="500">{{ old('shipping_address') }}</textarea>
                        </div>
                        <div class="space-y-2">
                            <label for="billing_address" class="block text-sm font-medium text-blue-100 flex items-center">
                                <i class="fas fa-receipt mr-2 text-blue-300"></i> Billing Address
                            </label>
                            <textarea id="billing_address" name="billing_address" rows="2" class="w-full bg-gray-800/70 text-white border border-gray-600/50 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 shadow-sm" required minlength="10" maxlength="500">{{ old('billing_address') }}</textarea>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="space-y-2">
                        <label for="payment_method" class="block text-sm font-medium text-blue-100 flex items-center">
                            <i class="fas fa-credit-card mr-2 text-blue-300"></i> Payment Method
                        </label>
                        <select id="payment_method" name="payment_method" class="w-full bg-gray-800/70 text-white border border-gray-600/50 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 shadow-sm" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                        </select>
                    </div>

                    <!-- Order Item Section -->
                    <div class="bg-white/5 rounded-xl p-6 border border-white/10">
                        <h2 class="text-lg font-bold text-white mb-4 flex items-center">
                            <i class="fas fa-wheat-alt mr-2 text-yellow-300"></i> Order Item Details
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="product_id" class="block text-sm font-medium text-blue-100">Product</label>
                                <select id="product_id" name="items[0][product_id]" class="w-full bg-gray-800/70 text-white border border-gray-600/50 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 shadow-sm" required>
                                    <option value="">Select Wheat Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('items.0.product_id', request('product_id')) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label for="quantity" class="block text-sm font-medium text-blue-100">Quantity (kg)</label>
                                <div class="relative">
                                    <input type="number" id="quantity" name="items[0][quantity]" class="w-full bg-gray-800/70 text-white border border-gray-600/50 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 shadow-sm" min="1" value="{{ old('items.0.quantity', 1) }}" required>
                                    <span class="absolute right-3 top-3 text-blue-300/70">kg</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label for="unit_price" class="block text-sm font-medium text-blue-100">Unit Price (per kg)</label>
                                <div class="relative">
                                    <input type="number" id="unit_price" name="items[0][unit_price]" class="w-full bg-gray-800/70 text-white border border-gray-600/50 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 shadow-sm" min="0" step="0.01" value="{{ old('items.0.unit_price') }}" required>
                                    <span class="absolute right-3 top-3 text-blue-300/70">$</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2">
                        <label for="notes" class="block text-sm font-medium text-blue-100 flex items-center">
                            <i class="far fa-sticky-note mr-2 text-blue-300"></i> Additional Notes
                        </label>
                        <textarea id="notes" name="notes" rows="3" class="w-full bg-gray-800/70 text-white border border-gray-600/50 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 shadow-sm" maxlength="1000">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold px-8 py-3 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Create Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection