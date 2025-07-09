<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Supplier Dashboard - Payments</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-gradient: linear-gradient(135deg, #2d1b69 0%, #11998e 100%);
            --card-gradient: linear-gradient(145deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
        }

        .font-space {
            font-family: 'Space Grotesk', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .sidebar {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: fixed;
            top: 5rem;
            left: 0;
            height: 100vh;
            z-index: 40;
            overflow-y: auto;
        }

        .sidebar-item {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sidebar-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .sidebar-item:hover::before,
        .sidebar-item.active::before {
            left: 0;
        }

        .sidebar-item:hover,
        .sidebar-item.active {
            color: white;
            transform: translateX(5px);
        }

        .gradient-text {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card {
            background: var(--card-gradient);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(20px, -20px);
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--secondary-gradient);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .btn-primary:hover::before {
            left: 0;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        .notification-dot {
            background: var(--secondary-gradient);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                height: 100vh;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
        }

        .logo-pulse {
            animation: logoPulse 2s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-gradient);
            border-radius: 3px;
        }

        /* Enhanced Form Input Styling */
        .form-input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .form-input-glass:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
            outline: none;
        }

        .form-input-glass::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-input-glass:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Select Styling */
        .form-select-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .form-select-glass:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
            outline: none;
        }

        .form-select-glass:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .form-select-glass option {
            background: #1f2937;
            color: white;
        }

        .table-row {
            transition: all 0.3s ease;
        }

        .table-row:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(5px);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-paid { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .status-pending { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
        .status-overdue { background: rgba(239, 68, 68, 0.2); color: #f87171; }

        .payment-method {
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            background: rgba(79, 172, 254, 0.2);
            color: #60a5fa;
        }

        .amount-positive { color: #4ade80; }
        .amount-negative { color: #f87171; }
        .amount-neutral { color: #fbbf24; }

        .days-early {
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        .days-late {
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        /* Fixed Navigation Bar */
        .fixed-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Main content adjustment for fixed elements */
        .main-content {
            margin-left: 16rem; /* 256px for sidebar width */
            margin-top: 5rem; /* 80px for navigation height */
            min-height: calc(100vh - 5rem);
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                height: 100vh;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                margin-top: 5rem;
            }
        }
    </style>
</head>
<body class="text-white overflow-x-hidden">
    <!-- Top Navigation -->
    <nav class="fixed-nav px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <button class="md:hidden text-white" id="sidebarToggle">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center logo-pulse">
                <span class="text-white font-bold">S</span>
            </div>
            <div>
                <h1 class="text-xl font-bold font-space gradient-text">SWSS Supplier</h1>
                <p class="text-xs text-gray-400">Wheat Supply Chain Management</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-6">
            <div class="relative">
                <i class="fas fa-bell text-gray-300 text-xl cursor-pointer hover:text-white transition-colors"></i>
                <span class="notification-dot absolute -top-1 -right-1 w-3 h-3 rounded-full"></span>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm font-semibold">John Supplier</p>
                    <p class="text-xs text-gray-400">Supplier Portal</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">JS</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="sidebar w-64 p-6" id="sidebar">
            <nav class="space-y-2">
                <a href="{{ route('supplier.dashboard') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="{{ route('inventory.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-seedling w-5"></i>
                    <span class="font-medium">Inventory</span>
                </a>
                <a href="{{ route('orders.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="font-medium">Orders</span>
                </a>
                <a href="/deliveries" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-truck w-5"></i>
                    <span class="font-medium">Deliveries</span>
                </a>
                <a href="/reports" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span class="font-medium">Reports</span>
                </a>
                <a href="/contracts" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-handshake w-5"></i>
                    <span class="font-medium">Contracts</span>
                </a>
                <a href="/payments" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-dollar-sign w-5"></i>
                    <span class="font-medium">Payments</span>
                </a>
                <a href="/chat" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 relative">
                    <i class="fas fa-comments w-5"></i>
                    <span class="font-medium">Chat</span>
                    <span class="notification-dot absolute -top-1 -right-1 w-3 h-3 rounded-full"></span>
                </a>
                <a href="/profile-settings" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-user-cog w-5"></i>
                    <span class="font-medium">Profile Settings</span>
                </a>
                
                <div class="pt-6 mt-6 border-t border-gray-700">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-red-400 hover:text-red-300 w-full text-left">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content p-6">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold font-space gradient-text">Payment Management</h1>
                        <p class="mt-2 text-sm text-gray-400">Track and manage your payment transactions</p>
                    </div>
                    <div class="flex space-x-3">
                        <button class="btn-primary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
                            <i class="fas fa-plus mr-2"></i>
                            New Invoice
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card p-6 rounded-2xl text-center float-animation">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dollar-sign text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">${{ number_format($analytics['total_amount'] / 1000, 0) }}K</h3>
                    <p class="text-gray-400 mb-2">Total Invoiced</p>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.2s">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">${{ number_format($analytics['paid_amount'] / 1000, 0) }}K</h3>
                    <p class="text-gray-400 mb-2">Paid Amount</p>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.4s">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">${{ number_format($analytics['pending_amount'] / 1000, 0) }}K</h3>
                    <p class="text-gray-400 mb-2">Pending Amount</p>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.6s">
                    <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">${{ number_format($analytics['overdue_amount'] / 1000, 0) }}K</h3>
                    <p class="text-gray-400 mb-2">Overdue Amount</p>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="stat-card p-6 rounded-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold font-space text-green-400">{{ $analytics['paid_invoices'] }}</h3>
                            <p class="text-gray-400">Paid Invoices</p>
                        </div>
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check text-green-400"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold font-space text-yellow-400">{{ $analytics['pending_invoices'] }}</h3>
                            <p class="text-gray-400">Pending Invoices</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-400"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold font-space text-red-400">{{ $analytics['overdue_invoices'] }}</h3>
                            <p class="text-gray-400">Overdue Invoices</p>
                        </div>
                        <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="glass-card p-6 rounded-2xl mb-6">
                <h3 class="text-lg font-medium font-space mb-4">Filters</h3>
                <form method="GET" action="/payments" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-300 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               class="form-input-glass w-full rounded-lg px-3 py-2" 
                               placeholder="Invoice or contract number">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                        <select name="status" id="status" class="form-select-glass w-full rounded-lg px-3 py-2">
                            <option value="">All Status</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-300 mb-1">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-select-glass w-full rounded-lg px-3 py-2">
                            <option value="">All Methods</option>
                            <option value="Bank Transfer" {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Credit Card" {{ request('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="Check" {{ request('payment_method') == 'Check' ? 'selected' : '' }}>Check</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full px-6 py-2 rounded-xl font-semibold text-sm text-white uppercase tracking-wider hover:transform hover:scale-105 transition-all duration-300">
                            <i class="fas fa-search mr-2"></i>
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            <!-- Payments Table -->
            <div class="glass-card rounded-2xl">
                @if($filteredPayments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Invoice</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Payment Method</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Timing</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Late Fees</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filteredPayments as $payment)
                                    <tr class="table-row border-b border-gray-800">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-green-500 to-teal-600 flex items-center justify-center">
                                                        <i class="fas fa-file-invoice-dollar text-sm text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-white">{{ $payment['invoice_number'] }}</div>
                                                    <div class="text-sm text-gray-400">{{ $payment['contract_number'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-white">{{ $payment['customer'] }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-white">${{ number_format($payment['amount'], 2) }}</div>
                                            @if($payment['late_fees'] > 0)
                                                <div class="text-sm text-red-400">+${{ number_format($payment['late_fees'], 2) }} fees</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($payment['status'] == 'paid')
                                                <span class="status-badge status-paid">
                                                    <i class="fas fa-check-circle"></i>
                                                    Paid
                                                </span>
                                            @elseif($payment['status'] == 'pending')
                                                <span class="status-badge status-pending">
                                                    <i class="fas fa-clock"></i>
                                                    Pending
                                                </span>
                                            @else
                                                <span class="status-badge status-overdue">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Overdue
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-white">{{ \Carbon\Carbon::parse($payment['due_date'])->format('M d, Y') }}</div>
                                            <div class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($payment['due_date'])->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($payment['payment_method'])
                                                <span class="payment-method">{{ $payment['payment_method'] }}</span>
                                                @if($payment['reference_number'])
                                                    <div class="text-xs text-gray-400 mt-1">{{ $payment['reference_number'] }}</div>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($payment['status'] == 'paid')
                                                @if($payment['days_early'] > 0)
                                                    <span class="days-early">
                                                        <i class="fas fa-arrow-up mr-1"></i>
                                                        {{ $payment['days_early'] }} days early
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">On time</span>
                                                @endif
                                            @elseif($payment['status'] == 'overdue')
                                                @php
                                                    $daysLate = \Carbon\Carbon::parse($payment['due_date'])->diffInDays(now());
                                                @endphp
                                                <span class="days-late">
                                                    <i class="fas fa-arrow-down mr-1"></i>
                                                    {{ $daysLate }} days late
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($payment['late_fees'] > 0)
                                                <div class="text-sm font-medium text-red-400">${{ number_format($payment['late_fees'], 2) }}</div>
                                            @else
                                                <div class="text-sm text-gray-400">$0.00</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-3">
                                                <a href="#" class="text-blue-400 hover:text-blue-300 transition-colors" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="#" class="text-green-400 hover:text-green-300 transition-colors" title="Download Invoice">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if($payment['status'] == 'pending')
                                                    <a href="#" class="text-purple-400 hover:text-purple-300 transition-colors" title="Send Reminder">
                                                        <i class="fas fa-bell"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-dollar-sign text-3xl text-white"></i>
                        </div>
                        <h3 class="text-lg font-medium text-white mb-2">No payments found</h3>
                        <p class="text-gray-400 mb-6">Get started by creating your first invoice.</p>
                        <button class="btn-primary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
                            <i class="fas fa-plus mr-2"></i>
                            New Invoice
                        </button>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });

        // Add hover effects to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Add hover effects to table rows
        document.querySelectorAll('.table-row').forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.transform = 'translateX(5px)';
            });
            
            row.addEventListener('mouseleave', () => {
                row.style.transform = 'translateX(0)';
            });
        });
    </script>
</body>
</html> 