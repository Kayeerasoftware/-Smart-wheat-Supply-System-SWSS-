@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Your Personalized Discounts</h1>
        @if(Auth::user()->role === 'admin')
        <form method="POST" action="{{ route('discounts.calculate') }}" class="inline">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Recalculate Discounts
            </button>
        </form>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($discounts && !empty($discounts['discounts']))
        <!-- Customer Segment Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h2 class="text-lg font-semibold text-blue-800 mb-2">Your Customer Segment: {{ $discounts['segment'] }}</h2>
            <p class="text-blue-600">Based on your purchasing behavior, you qualify for these special offers!</p>
        </div>

        <!-- Available Discounts -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($discounts['discounts'] as $discount)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border-2 border-green-200">
                <div class="bg-green-500 text-white px-4 py-3">
                    <h3 class="text-lg font-semibold">{{ $discount['type'] }}</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">{{ $discount['description'] }}</p>
                    
                    <div class="text-center mb-4">
                        <span class="text-3xl font-bold text-green-600">{{ $discount['discount_percentage'] }}%</span>
                        <span class="text-gray-500">OFF</span>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-4">{{ $discount['savings_message'] }}</p>
                    
                    <!-- Discount Details -->
                    <div class="space-y-2 text-sm text-gray-600">
                        @if(isset($discount['minimum_items']))
                            <p>📦 Minimum items: {{ $discount['minimum_items'] }}</p>
                        @endif
                        @if(isset($discount['minimum_products']))
                            <p>🛍️ Minimum products: {{ $discount['minimum_products'] }}</p>
                        @endif
                        @if(isset($discount['minimum_order']))
                            <p>💰 Minimum order: ${{ number_format($discount['minimum_order']) }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- How to Qualify -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-yellow-800 mb-4">How to Qualify for These Discounts</h3>
            
            @if($discounts['segment'] === 'Frequent Customers')
                <div class="space-y-3">
                    <div class="flex items-start">
                        <span class="text-yellow-600 mr-3">📦</span>
                        <div>
                            <p class="font-semibold">Bulk Purchase (20% off)</p>
                            <p class="text-sm text-gray-600">Add 5 or more items to your cart</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="text-yellow-600 mr-3">🛍️</span>
                        <div>
                            <p class="font-semibold">Bundle Deal (15% off)</p>
                            <p class="text-sm text-gray-600">Purchase 3 or more different products</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="text-yellow-600 mr-3">🔄</span>
                        <div>
                            <p class="font-semibold">Auto-Reorder (5% off)</p>
                            <p class="text-sm text-gray-600">Set up subscription orders for recurring products</p>
                        </div>
                    </div>
                </div>
            @elseif($discounts['segment'] === 'Big Spenders')
                <div class="space-y-3">
                    <div class="flex items-start">
                        <span class="text-yellow-600 mr-3">💎</span>
                        <div>
                            <p class="font-semibold">Premium Bulk Pricing</p>
                            <p class="text-sm text-gray-600">Orders over $1,000 get 5-25% off (scales with order value)</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="text-yellow-600 mr-3">🎯</span>
                        <div>
                            <p class="font-semibold">Personalized Deals</p>
                            <p class="text-sm text-gray-600">Based on your purchase history and average order value</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="text-yellow-600 mr-3">🎉</span>
                        <div>
                            <p class="font-semibold">Exclusive Events</p>
                            <p class="text-sm text-gray-600">Special holiday and seasonal discounts</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Current Order Summary -->
        @if(isset($discounts['order_total']) && $discounts['order_total'] > 0)
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Current Order</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600">Order Total</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($discounts['order_total'], 2) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Total Discount</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($discounts['total_savings'], 2) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Final Total</p>
                    <p class="text-2xl font-bold text-blue-600">${{ number_format($discounts['final_total'], 2) }}</p>
                </div>
            </div>
        </div>
        @endif

    @else
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <p class="font-semibold">No discounts available yet!</p>
            <p class="text-sm mt-1">Complete your first order to start earning personalized discounts based on your purchasing behavior.</p>
        </div>
    @endif
</div>
@endsection 