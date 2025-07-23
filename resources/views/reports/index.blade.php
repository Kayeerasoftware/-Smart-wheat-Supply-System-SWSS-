<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Supplier Dashboard - Reports</title>
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

        .form-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .table-row {
            transition: all 0.3s ease;
        }

        .table-row:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(5px);
        }

        .progress-bar {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 8px;
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .chart-container {
            position: relative;
            height: 300px;
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
            <a href="/reports" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                <i class="fas fa-chart-bar w-5"></i>
                <span class="font-medium">Reports</span>
            </a>
            <a href="/contracts" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-handshake w-5"></i>
                <span class="font-medium">Contracts</span>
            </a>
            <a href="/payments" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
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
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold font-space gradient-text">Analytics & Reports</h1>
                    <p class="mt-2 text-sm text-gray-400">Comprehensive insights into your business performance</p>
                </div>
                <div class="flex flex-col md:flex-row gap-3 items-center">
                    <!-- Generate Now Button -->
                    <button onclick="window.location.reload()" class="flex items-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-md">
                        <i class="fas fa-play mr-2"></i>
                        Generate Now
                    </button>
                    <!-- Quick Report Buttons -->
                    <div class="flex gap-2">
                        <button onclick="exportQuickReport('inventory', 'pdf')" class="flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-boxes mr-2"></i>Inventory PDF
                        </button>
                        <button onclick="exportQuickReport('orders', 'pdf')" class="flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-shopping-cart mr-2"></i>Orders PDF
                        </button>
                        <button onclick="exportQuickReport('deliveries', 'pdf')" class="flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-truck mr-2"></i>Deliveries PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card p-6 rounded-2xl text-center float-animation">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-dollar-sign text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold font-space mb-2">${{ number_format($analytics['total_revenue'] ?? 0, 0) }}</h3>
                <p class="text-gray-400 mb-2">Total Revenue</p>
            </div>

            <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.2s">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shopping-cart text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold font-space mb-2">{{ number_format($analytics['total_orders'] ?? 0) }}</h3>
                <p class="text-gray-400 mb-2">Total Orders</p>
            </div>

            <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.4s">
                <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-warehouse text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold font-space mb-2">${{ number_format($analytics['total_inventory_value'] ?? 0, 0) }}</h3>
                <p class="text-gray-400 mb-2">Inventory Value</p>
            </div>

            <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.6s">
                <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-truck text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold font-space mb-2">{{ number_format($analytics['total_shipments'] ?? 0) }}</h3>
                <p class="text-gray-400 mb-2">Total Shipments</p>
            </div>
        </div>

        <!-- Auto Report Settings & Status -->
        <div class="glass-card p-6 rounded-2xl mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium font-space flex items-center">
                    <i class="fas fa-robot mr-3 text-blue-400"></i>
                    Automatic Report Settings
                </h3>
                <span class="px-3 py-1 bg-green-500 text-white text-xs font-medium rounded-full">Active</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Current Settings -->
                <div class="bg-gray-800 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-white mb-3">Current Settings</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Frequency:</span>
                            <span class="text-white">Weekly</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Delivery:</span>
                            <span class="text-white">Email</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Report Types:</span>
                            <span class="text-white">3 Active</span>
                        </div>
                    </div>
                </div>

                <!-- Next Scheduled Report -->
                <div class="bg-gray-800 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-white mb-3">Next Scheduled Report</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Date:</span>
                            <span class="text-white">Monday, Dec 23, 2024</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Time:</span>
                            <span class="text-white">9:00 AM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Type:</span>
                            <span class="text-white">Weekly Summary</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Reports -->
                <div class="bg-gray-800 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-white mb-3">Recent Reports</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Dec 16, 2024</span>
                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">Sent</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Dec 9, 2024</span>
                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">Sent</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Dec 2, 2024</span>
                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">Sent</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-4 pt-4 border-t border-gray-700">
                <div class="flex space-x-3">
                    <button class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-play mr-2"></i>
                        Generate Now
                    </button>
                    <button class="flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-history mr-2"></i>
                        View History
                    </button>
                    <button class="flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-pause mr-2"></i>
                        Pause Auto Reports
                    </button>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue Trend Chart -->
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-lg font-medium font-space mb-4">Revenue Trend (12 Months)</h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Orders Trend Chart -->
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-lg font-medium font-space mb-4">Orders Trend (12 Months)</h3>
                <div class="chart-container">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Delivery Performance -->
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-lg font-medium font-space mb-4">Delivery Performance</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-300">Delivered</span>
                            <span class="text-sm font-medium">{{ $analytics['delivered_shipments'] ?? 0 }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill bg-green-500" style="width: {{ $analytics['total_shipments'] > 0 ? ($analytics['delivered_shipments'] / $analytics['total_shipments']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-300">In Transit</span>
                            <span class="text-sm font-medium">{{ $analytics['in_transit_shipments'] ?? 0 }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill bg-yellow-500" style="width: {{ $analytics['total_shipments'] > 0 ? ($analytics['in_transit_shipments'] / $analytics['total_shipments']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Status -->
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-lg font-medium font-space mb-4">Inventory Status</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-300">Low Stock Items</span>
                            <span class="text-sm font-medium">{{ $analytics['low_stock_items'] ?? 0 }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill bg-yellow-500" style="width: {{ $analytics['low_stock_items'] > 0 ? ($analytics['low_stock_items'] / max(1, $analytics['low_stock_items'] + $analytics['out_of_stock_items'])) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-300">Out of Stock</span>
                            <span class="text-sm font-medium">{{ $analytics['out_of_stock_items'] ?? 0 }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill bg-red-500" style="width: {{ $analytics['out_of_stock_items'] > 0 ? ($analytics['out_of_stock_items'] / max(1, $analytics['low_stock_items'] + $analytics['out_of_stock_items'])) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-lg font-medium font-space mb-4">Top Products by Revenue</h3>
                <div class="space-y-3">
                    @forelse($topProducts ?? [] as $product)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium">{{ $product['product']->name ?? 'Unknown Product' }}</p>
                                <p class="text-xs text-gray-400">{{ number_format($product['total_quantity']) }} units</p>
                            </div>
                            <span class="text-sm font-bold">${{ number_format($product['total_revenue'], 0) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No product data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Warehouse Performance Table -->
        <div class="glass-card rounded-2xl">
            <div class="p-6 border-b border-gray-700">
                <h3 class="text-lg font-medium font-space">Warehouse Performance</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Warehouse</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Items</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Inventory Value</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Low Stock Items</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehousePerformance ?? [] as $warehouse)
                            <tr class="table-row border-b border-gray-800">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                                <i class="fas fa-warehouse text-sm text-white"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white">{{ $warehouse['warehouse']->name ?? 'Unknown Warehouse' }}</div>
                                            <div class="text-sm text-gray-400">{{ $warehouse['warehouse']->location ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-white">{{ number_format($warehouse['total_items']) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-white">${{ number_format($warehouse['total_value'], 0) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-400">{{ number_format($warehouse['low_stock_items']) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $performance = $warehouse['total_items'] > 0 ? (($warehouse['total_items'] - $warehouse['low_stock_items']) / $warehouse['total_items']) * 100 : 0;
                                    @endphp
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-700 rounded-full h-2 mr-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $performance }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-300">{{ number_format($performance, 0) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b border-gray-800">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-400">
                                    No warehouse data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

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

        // Chart.js Configuration
        Chart.defaults.color = '#9ca3af';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($monthlyData ?? [], 'month')) !!},
                datasets: [{
                    label: 'Revenue ($)',
                    data: {!! json_encode(array_column($monthlyData ?? [], 'revenue')) !!},
                    borderColor: '#4facfe',
                    backgroundColor: 'rgba(79, 172, 254, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // Orders Chart
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($monthlyData ?? [], 'month')) !!},
                datasets: [{
                    label: 'Orders',
                    data: {!! json_encode(array_column($monthlyData ?? [], 'orders')) !!},
                    backgroundColor: 'rgba(139, 92, 246, 0.8)',
                    borderColor: '#8b5cf6',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        function exportQuickReport(type, format) {
            alert('Generating ' + type.charAt(0).toUpperCase() + type.slice(1) + ' report as ' + format.toUpperCase() + '...');
            // You can replace this with an AJAX call to the backend for real report generation
            window.location.href = `/reports/export/${format}?type=${type}`;
        }
    </script>
</body>
</html> 