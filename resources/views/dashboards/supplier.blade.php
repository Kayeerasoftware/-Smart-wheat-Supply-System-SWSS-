<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Supplier Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        .chart-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
        }

        .activity-item {
            background: rgba(255, 255, 255, 0.03);
            border-left: 3px solid transparent;
            border-image: var(--accent-gradient) 1;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
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

        .progress-bar {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            background: var(--accent-gradient);
            height: 8px;
            border-radius: 10px;
            transition: width 2s ease;
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
    </style>
</head>
<body class="text-white overflow-x-hidden">
    <!-- Top Navigation -->
    <nav class="glass-card mx-4 mt-4 mb-6 px-6 py-4 flex justify-between items-center">
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
        <aside class="sidebar w-64 min-h-screen p-6" id="sidebar">
            <nav class="space-y-2">
                <a href="#" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-warehouse w-5"></i>
                    <span class="font-medium">Inventory</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="font-medium">Orders</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-truck w-5"></i>
                    <span class="font-medium">Deliveries</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span class="font-medium">Reports</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-handshake w-5"></i>
                    <span class="font-medium">Contracts</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-dollar-sign w-5"></i>
                    <span class="font-medium">Payments</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
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
        <main class="flex-1 p-6">
            @if(isset($vendor) && $vendor && $vendor->facilityVisits->where('status', 'scheduled')->count() > 0)
                @php
                    $nextVisit = $vendor->facilityVisits->where('status', 'scheduled')->sortBy('scheduled_at')->first();
                @endphp
                <div class="alert alert-info bg-blue-100 text-blue-900 border border-blue-300 rounded-lg p-4 mb-6">
                    <strong>Upcoming Facility Visit:</strong><br>
                    <span>Date: {{ $nextVisit->scheduled_at->format('M d, Y') }}</span><br>
                    <span>Notes: {{ $nextVisit->notes ?? 'No notes' }}</span>
                </div>
            @endif
            @if(isset($supplierInventory) && $supplierInventory->count() > 0)
                <div class="glass-card p-6 rounded-2xl mb-8">
                    <h2 class="text-2xl font-bold font-space mb-6">Your Inventory</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="py-3 px-4 font-semibold">Product</th>
                                    <th class="py-3 px-4 font-semibold">Warehouse</th>
                                    <th class="py-3 px-4 font-semibold">On Hand</th>
                                    <th class="py-3 px-4 font-semibold">Available</th>
                                    <th class="py-3 px-4 font-semibold">Reserved</th>
                                    <th class="py-3 px-4 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplierInventory as $inv)
                                    <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition-colors">
                                        <td class="py-4 px-4">{{ $inv->product->name ?? 'N/A' }}</td>
                                        <td class="py-4 px-4">{{ $inv->warehouse->name ?? 'N/A' }}</td>
                                        <td class="py-4 px-4">{{ $inv->quantity_on_hand }}</td>
                                        <td class="py-4 px-4">{{ $inv->quantity_available }}</td>
                                        <td class="py-4 px-4">{{ $inv->quantity_reserved }}</td>
                                        <td class="py-4 px-4">
                                            @if($inv->quantity_available <= 0)
                                                <span class="px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs">Out of Stock</span>
                                            @elseif($inv->quantity_available <= 10)
                                                <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Low Stock</span>
                                            @else
                                                <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">In Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            <!-- Analytics Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card p-6 rounded-2xl text-center float-animation">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-warehouse text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ number_format($totalInventory ?? 0) }}</h3>
                    <p class="text-gray-400 mb-2">Total Inventory (units)</p>
                </div>
                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.2s">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dollar-sign text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">${{ number_format($totalInventoryValue ?? 0, 2) }}</h3>
                    <p class="text-gray-400 mb-2">Inventory Value</p>
                </div>
                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.4s">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $lowStockItems ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Low Stock Items</p>
                </div>
                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.6s">
                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-ban text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $outOfStockItems ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Out of Stock</p>
                </div>
            </div>

            <!-- Filter/Search Form -->
            <form method="GET" class="mb-6 flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm mb-1">Product Search</label>
                    <input type="text" name="search" value="{{ $filter_search ?? '' }}" class="form-input rounded border-gray-300" placeholder="Name or SKU">
                </div>
                <div>
                    <label class="block text-sm mb-1">Warehouse</label>
                    <select name="warehouse" class="form-select rounded border-gray-300">
                        <option value="">All</option>
                        @foreach($warehouses ?? [] as $wh)
                            <option value="{{ $wh->id }}" @if(($filter_warehouse ?? '') == $wh->id) selected @endif>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Status</label>
                    <select name="status" class="form-select rounded border-gray-300">
                        <option value="">All</option>
                        <option value="in" @if(($filter_status ?? '') == 'in') selected @endif>In Stock</option>
                        <option value="low" @if(($filter_status ?? '') == 'low') selected @endif>Low Stock</option>
                        <option value="out" @if(($filter_status ?? '') == 'out') selected @endif>Out of Stock</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-primary px-6 py-2 rounded-xl font-semibold">Filter</button>
                </div>
            </form>

            <!-- Inventory Trend Chart -->
            <div class="glass-card p-6 rounded-2xl mb-8">
                <h2 class="text-xl font-bold font-space mb-4">Inventory Trend</h2>
                <canvas id="inventoryTrendChart" height="100"></canvas>
            </div>

            <!-- Inventory Table -->
            @if(isset($filteredInventory) && $filteredInventory->count() > 0)
                <div class="glass-card p-6 rounded-2xl mb-8">
                    <h2 class="text-2xl font-bold font-space mb-6">Filtered Inventory</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="py-3 px-4 font-semibold">Product</th>
                                    <th class="py-3 px-4 font-semibold">Warehouse</th>
                                    <th class="py-3 px-4 font-semibold">On Hand</th>
                                    <th class="py-3 px-4 font-semibold">Available</th>
                                    <th class="py-3 px-4 font-semibold">Reserved</th>
                                    <th class="py-3 px-4 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filteredInventory as $inv)
                                    <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition-colors">
                                        <td class="py-4 px-4">{{ $inv->product->name ?? 'N/A' }}</td>
                                        <td class="py-4 px-4">{{ $inv->warehouse->name ?? 'N/A' }}</td>
                                        <td class="py-4 px-4">{{ $inv->quantity_on_hand }}</td>
                                        <td class="py-4 px-4">{{ $inv->quantity_available }}</td>
                                        <td class="py-4 px-4">{{ $inv->quantity_reserved }}</td>
                                        <td class="py-4 px-4">
                                            @if($inv->quantity_available <= 0)
                                                <span class="px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs">Out of Stock</span>
                                            @elseif($inv->quantity_available <= 10)
                                                <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Low Stock</span>
                                            @else
                                                <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">In Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Main Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Inventory Performance Chart -->
                <div class="lg:col-span-2">
                    <div class="glass-card p-6 rounded-2xl">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold font-space">Inventory Performance</h2>
                            <select class="bg-transparent border border-gray-600 rounded-lg px-3 py-1 text-sm">
                                <option>Last 7 days</option>
                                <option>Last 30 days</option>
                                <option>Last 90 days</option>
                            </select>
                        </div>
                        <div class="chart-container h-64 flex items-center justify-center">
                            <canvas id="inventoryChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="glass-card p-6 rounded-2xl">
                    <h2 class="text-xl font-bold font-space mb-6">Quick Actions</h2>
                    <div class="space-y-4">
                        <a href="{{ route('products.create') }}" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center space-x-2 relative overflow-hidden">
                            <i class="fas fa-plus"></i>
                            <span>New Order</span>
                        </a>
                        <a href="{{ route('inventory.index') }}" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center space-x-2 relative overflow-hidden">
                            <i class="fas fa-sync-alt"></i>
                            <span>Update Inventory</span>
                        </a>
                        <a href="{{ route('products.analytics') }}" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center space-x-2 relative overflow-hidden">
                            <i class="fas fa-chart-line"></i>
                            <span>View Reports</span>
                        </a>
                        <a href="#" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center space-x-2 relative overflow-hidden" onclick="alert('Delivery tracking coming soon!')">
                            <i class="fas fa-truck"></i>
                            <span>Track Deliveries</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-8">
                <div class="glass-card p-6 rounded-2xl">
                    <h2 class="text-2xl font-bold font-space mb-6">Recent Activity</h2>
                    <div class="space-y-4">
                        <div class="activity-item p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">Order #12345 delivered successfully</p>
                                        <p class="text-sm text-gray-400">Dec 15, 2024 14:30</p>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-400">2 hours ago</span>
                            </div>
                        </div>
                        
                            <div class="activity-item p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-plus text-white text-sm"></i>
                                        </div>
                                        <div>
                                        <p class="font-medium">New order received from Miller Co.</p>
                                        <p class="text-sm text-gray-400">Dec 15, 2024 12:15</p>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-400">4 hours ago</span>
                            </div>
                        </div>
                        
                        <div class="activity-item p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                                        </div>
                                    <div>
                                        <p class="font-medium">Low stock alert: Premium Wheat Grade A</p>
                                        <p class="text-sm text-gray-400">Dec 15, 2024 09:45</p>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-400">7 hours ago</span>
                            </div>
                        </div>
                        
                            <div class="activity-item p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-dollar-sign text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">Payment received for Order #12340</p>
                                        <p class="text-sm text-gray-400">Dec 14, 2024 16:20</p>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-400">Yesterday</span>
                            </div>
                        </div>
                    </div>
                </div>
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

        window.addEventListener('load', () => {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });
        });

        const canvas = document.getElementById('inventoryChart');
        const ctx = canvas.getContext('2d');
        
        // Sample inventory data
        const data = [45, 52, 48, 61, 58, 55, 62, 59, 68, 72, 65, 70];
        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        function drawChart() {
            const canvasWidth = canvas.width;
            const canvasHeight = canvas.height;
            const padding = 40;
            const chartWidth = canvasWidth - 2 * padding;
            const chartHeight = canvasHeight - 2 * padding;
            
            ctx.clearRect(0, 0, canvasWidth, canvasHeight);
            
            const gradient = ctx.createLinearGradient(0, 0, 0, canvasHeight);
            gradient.addColorStop(0, 'rgba(79, 172, 254, 0.8)');
            gradient.addColorStop(1, 'rgba(79, 172, 254, 0.1)');
            
            ctx.fillStyle = gradient;
            ctx.beginPath();
            ctx.moveTo(padding, canvasHeight - padding);
            
            data.forEach((value, index) => {
                const x = padding + (index * chartWidth) / (data.length - 1);
                const y = canvasHeight - padding - (value / 100) * chartHeight;
                
                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            });
            
            ctx.lineTo(canvasWidth - padding, canvasHeight - padding);
            ctx.lineTo(padding, canvasHeight - padding);
            ctx.closePath();
            ctx.fill();
            
            ctx.strokeStyle = '#4FACFE';
            ctx.lineWidth = 3;
            ctx.beginPath();
            
            data.forEach((value, index) => {
                const x = padding + (index * chartWidth) / (data.length - 1);
                const y = canvasHeight - padding - (value / 100) * chartHeight;
                
                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            });
            
            ctx.stroke();
            
            data.forEach((value, index) => {
                const x = padding + (index * chartWidth) / (data.length - 1);
                const y = canvasHeight - padding - (value / 100) * chartHeight;
                
                ctx.beginPath();
                ctx.arc(x, y, 4, 0, 2 * Math.PI);
                ctx.fillStyle = '#4FACFE';
                ctx.fill();
                ctx.strokeStyle = 'white';
                ctx.lineWidth = 2;
                ctx.stroke();
            });
        }
        
        setTimeout(drawChart, 1000);
        
        window.addEventListener('resize', () => {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            drawChart();
        });

        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Simulate real-time updates
        setInterval(() => {
            const randomCard = document.querySelectorAll('.stat-card h3')[Math.floor(Math.random() * 4)];
            const currentValue = parseInt(randomCard.textContent.replace(/,/g, ''));
            const newValue = currentValue + Math.floor(Math.random() * 20) - 10;
            if (newValue > 0) {
                randomCard.textContent = newValue.toLocaleString();
            }
        }, 15000);

        const ctxTrend = document.getElementById('inventoryTrendChart').getContext('2d');
        const inventoryTrendChart = new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Inventory',
                    data: [45, 52, 48, 61, 58, 55, 62, 59, 68, 72, 65, 70], // Replace with real data
                    borderColor: '#4FACFE',
                    backgroundColor: 'rgba(79, 172, 254, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>