{{-- Farmer Analytics Page --}}
@extends('layouts.supplier')

@section('title', 'Farmer Analytics')

@section('sidebar')
    {{-- Sidebar navigation for farmer --}}
    <div class="sidebar w-64 py-8 px-4 flex flex-col h-full justify-between">
        <div>
            <div class="mb-8">
                <h2 class="text-2xl font-bold gradient-text mb-2">Farmer Panel</h2>
                <div class="text-sm text-gray-400">Welcome, {{ Auth::user()->name }}</div>
            </div>
            <nav class="flex flex-col gap-2">
                <a href="{{ route('farmer.dashboard') }}" class="sidebar-item {{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
                <a href="{{ route('farmer.orders.index') }}" class="sidebar-item {{ request()->routeIs('farmer.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart mr-2"></i> Orders
                </a>
                <a href="{{ route('inventory.index') }}" class="sidebar-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-warehouse mr-2"></i> Inventory
                </a>
                <a href="{{ route('farmer.analytics') }}" class="sidebar-item {{ request()->routeIs('farmer.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-line mr-2"></i> Analytics
                </a>
                <a href="{{ route('profile.edit') }}" class="sidebar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
            </nav>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="mt-8">
            @csrf
            <button type="submit" class="sidebar-item text-red-500 hover:text-red-700 flex items-center w-full">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-indigo-700 via-purple-700 to-indigo-900">
    {{-- Sidebar --}}
    @yield('sidebar')
    <div class="flex-1 flex flex-col">
        {{-- Navbar (if any) --}}
        @yield('navbar')
        <main class="p-8 flex-1">
            <div class="glass-card p-8 rounded-2xl text-center">
                <h1 class="text-4xl font-bold font-space mb-4 gradient-text">Farmer Analytics</h1>
                <p class="text-lg text-gray-300 mb-6">Your analytics dashboard will appear here soon.</p>
                <div class="text-gray-400">Coming soon: crop trends, sales, inventory, and more insights tailored for farmers.</div>
            </div>
        </main>
    </div>
</div>
@endsection 