<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Admin Dashboard - Supply Chain Management</title>
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

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-completed {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-in-progress {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
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

        .main-content {
            margin-left: 16rem;
            margin-top: 5rem;
            min-height: calc(100vh - 5rem);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                margin-top: 5rem;
            }
        }

        .metric-card {
            background: var(--card-gradient);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="text-white overflow-x-hidden">
    <!-- Top Navigation -->
    <nav class="fixed-nav px-6 py-4 flex justify-between items-center" style="position: fixed; top: 0; left: 0; right: 0; z-index: 50; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="flex items-center space-x-4">
            <button class="md:hidden text-white" id="sidebarToggle">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center logo-pulse">
                <span class="text-white font-bold">A</span>
            </div>
            <div>
                <h1 class="text-xl font-bold font-space gradient-text">SWSS Admin</h1>
                <p class="text-xs text-gray-400">System Administration</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-6">
            <div class="relative">
                <i class="fas fa-bell text-gray-300 text-xl cursor-pointer hover:text-white transition-colors"></i>
                <span class="notification-dot absolute -top-1 -right-1 w-3 h-3 rounded-full"></span>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm font-semibold">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-400">Administrator</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name ?? 'A', 0, 2) }}</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar w-64 p-6" id="sidebar">
        <nav class="space-y-2">
            <a href="{{ route('dashboard') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.vendors') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-building w-5"></i>
                <span class="font-medium">Vendor Management</span>
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-seedling w-5"></i>
                <span class="font-medium">Inventory</span>
            </a>
            <a href="{{ route('admin.supply-chain.index') }}" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                <i class="fas fa-truck w-5"></i>
                <span class="font-medium">Supply Chain</span>
            </a>
            <a href="{{ route('admin.analytics') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-chart-line w-5"></i>
                <span class="font-medium">Analytics</span>
            </a>
            <a href="{{ route('admin.system-settings.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-cog w-5"></i>
                <span class="font-medium">System Settings</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-file-alt w-5"></i>
                <span class="font-medium">Reports</span>
            </a>
            <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-shield-alt w-5"></i>
                <span class="font-medium">Audit Logs</span>
            </a>
            <a href="{{ route('chat.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 relative">
                <i class="fas fa-comments w-5"></i>
                <span class="font-medium">Chat</span>
                <span class="notification-dot absolute -top-1 -right-1 w-3 h-3 rounded-full"></span>
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
                    <h1 class="text-3xl font-bold font-space gradient-text">Supply Chain Management</h1>
                    <p class="mt-2 text-sm text-gray-400">Comprehensive oversight of orders, shipments, and manufacturing operations</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.supply-chain.analytics') }}" class="btn-primary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Analytics
                    </a>
                </div>
            </div>
        </div>

        <!-- Supply Chain Overview Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Orders</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($summary['total_orders'] ?? 0) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ number_format($summary['pending_orders'] ?? 0) }} pending</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Purchase Orders</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($summary['total_purchase_orders'] ?? 0) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ number_format($summary['pending_purchase_orders'] ?? 0) }} pending</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center">
                        <i class="fas fa-file-invoice text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Shipments</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($summary['total_shipments'] ?? 0) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ number_format($summary['in_transit_shipments'] ?? 0) }} in transit</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-shipping-fast text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Manufacturing</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($summary['total_manufacturing_orders'] ?? 0) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ number_format($summary['active_manufacturing_orders'] ?? 0) }} active</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 flex items-center justify-center">
                        <i class="fas fa-industry text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="metric-card p-6 rounded-2xl">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-percentage text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">{{ number_format($metrics['order_fulfillment_rate'] ?? 0, 1) }}%</h3>
                    <p class="text-gray-400 text-sm">Order Fulfillment Rate</p>
                </div>
            </div>

            <div class="metric-card p-6 rounded-2xl">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">{{ number_format($metrics['average_order_processing_time'] ?? 0, 1) }} days</h3>
                    <p class="text-gray-400 text-sm">Avg Processing Time</p>
                </div>
            </div>

            <div class="metric-card p-6 rounded-2xl">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-star text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">{{ number_format($metrics['supplier_performance_score'] ?? 0, 1) }}/100</h3>
                    <p class="text-gray-400 text-sm">Supplier Performance</p>
                </div>
            </div>

            <div class="metric-card p-6 rounded-2xl">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-sync-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">{{ number_format($metrics['inventory_turnover_rate'] ?? 0, 1) }}x</h3>
                    <p class="text-gray-400 text-sm">Inventory Turnover</p>
                </div>
            </div>
        </div>

        <!-- Supply Chain Management Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Orders Management -->
            <div class="glass-card p-6 rounded-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold font-space">Recent Orders</h2>
                    <a href="{{ route('admin.supply-chain.orders') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($recentOrders ?? [] as $order)
                        <div class="flex items-center justify-between p-4 rounded-lg bg-gray-800/30">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $order->order_number ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-400">{{ $order->user->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="status-badge status-{{ $order->status ?? 'pending' }}">
                                    {{ ucfirst($order->status ?? 'pending') }}
                                </span>
                                <p class="text-sm text-gray-400 mt-1">${{ number_format($order->total_amount ?? 0, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-4">No recent orders</p>
                    @endforelse
                </div>
            </div>

            <!-- Purchase Orders Management -->
            <div class="glass-card p-6 rounded-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold font-space">Recent Purchase Orders</h2>
                    <a href="{{ route('admin.supply-chain.purchase-orders') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($recentPurchaseOrders ?? [] as $po)
                        <div class="flex items-center justify-between p-4 rounded-lg bg-gray-800/30">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-file-invoice text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $po->po_number ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-400">{{ $po->supplier->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="status-badge status-{{ $po->status ?? 'pending' }}">
                                    {{ ucfirst($po->status ?? 'pending') }}
                                </span>
                                <p class="text-sm text-gray-400 mt-1">${{ number_format($po->total_amount ?? 0, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-4">No recent purchase orders</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Additional Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Shipments Management -->
            <div class="glass-card p-6 rounded-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold font-space">Recent Shipments</h2>
                    <a href="{{ route('admin.supply-chain.shipments') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($recentShipments ?? [] as $shipment)
                        <div class="flex items-center justify-between p-4 rounded-lg bg-gray-800/30">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shipping-fast text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $shipment->tracking_number ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-400">{{ $shipment->warehouse->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="status-badge status-{{ $shipment->status ?? 'pending' }}">
                                    {{ ucfirst($shipment->status ?? 'pending') }}
                                </span>
                                <p class="text-sm text-gray-400 mt-1">{{ $shipment->created_at->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-4">No recent shipments</p>
                    @endforelse
                </div>
            </div>

            <!-- Manufacturing Orders Management -->
            <div class="glass-card p-6 rounded-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold font-space">Recent Manufacturing Orders</h2>
                    <a href="{{ route('admin.supply-chain.manufacturing-orders') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($recentManufacturingOrders ?? [] as $mo)
                        <div class="flex items-center justify-between p-4 rounded-lg bg-gray-800/30">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-industry text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $mo->order_number ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-400">{{ $mo->product->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="status-badge status-{{ $mo->status ?? 'pending' }}">
                                    {{ ucfirst($mo->status ?? 'pending') }}
                                </span>
                                <p class="text-sm text-gray-400 mt-1">{{ number_format($mo->quantity ?? 0) }} units</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-4">No recent manufacturing orders</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <script>
        // Mobile sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });
    </script>
</body>
</html> 