{{-- Use the supplier layout for consistency --}}
@extends('layouts.supplier')

@section('title', 'Farmer Orders')

@section('sidebar')
    {{-- Sidebar navigation for farmer --}}
    <div class="sidebar w-64 py-8 px-4">
        <div class="mb-8">
            <h2 class="text-2xl font-bold gradient-text mb-2">Farmer Panel</h2>
            <div class="text-sm text-gray-400">Welcome, {{ Auth::user()->name }}</div>
        </div>
        <nav class="flex flex-col gap-2">
            <a href="{{ route('farmer.orders.index') }}" class="sidebar-item {{ request()->routeIs('farmer.orders.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart mr-2"></i> Orders
            </a>
            <a href="{{ route('farmer.analytics') }}" class="sidebar-item {{ request()->routeIs('farmer.analytics') ? 'active' : '' }}">
                <i class="fas fa-chart-line mr-2"></i> Analytics
            </a>
            <a href="{{ route('profile.edit') }}" class="sidebar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="fas fa-user mr-2"></i> Profile
            </a>
        </nav>
        <form action="{{ route('logout') }}" method="POST" class="mt-8">
            @csrf
            <button type="submit" class="sidebar-item text-red-500 hover:text-red-700 flex items-center w-full">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
        </form>
    </div>
@endsection

@section('navbar')
    @include('layouts.navigation')
@endsection

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-indigo-700 via-purple-700 to-indigo-900">
    @yield('sidebar')
    <div class="flex-1 flex flex-col">
        @yield('navbar')
        <main class="p-8">
            <div class="max-w-6xl mx-auto space-y-8">
                <!-- Header -->
                <div class="glass-card p-8 rounded-2xl flex items-center justify-between mb-8">
                    <h1 class="text-4xl font-bold font-space gradient-text">Farmer Orders</h1>
                    <a href="{{ route('orders.create') }}" class="btn-primary px-6 py-3 rounded-lg text-white font-bold shadow hover:scale-105 transition">
                        <i class="fas fa-plus mr-2"></i> New Order
                    </a>
                </div>
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="stat-card p-6 rounded-xl text-center">
                        <div class="text-2xl font-bold text-white">{{ $totalOrders ?? 0 }}</div>
                        <div class="text-gray-300 mt-2">Total Orders</div>
                    </div>
                    <div class="stat-card p-6 rounded-xl text-center">
                        <div class="text-2xl font-bold text-yellow-400">{{ $pendingOrders ?? 0 }}</div>
                        <div class="text-gray-300 mt-2">Pending</div>
                    </div>
                    <div class="stat-card p-6 rounded-xl text-center">
                        <div class="text-2xl font-bold text-green-400">{{ $completedOrders ?? 0 }}</div>
                        <div class="text-gray-300 mt-2">Completed</div>
                    </div>
                </div>
                <!-- Orders Table -->
                <div class="glass-card p-8 rounded-2xl mt-8">
                    @include('orders.partials.farmer-orders-content')
                </div>
            </div>
        </main>
    </div>
</div>
@endsection 