<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Supplier Dashboard - Inventory Details</title>
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

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-in-stock {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-low-stock {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-out-of-stock {
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
                    <p class="text-sm font-semibold">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-400">Supplier Portal</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name ?? 'U', 0, 2) }}</span>
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
            <a href="{{ route('inventory.index') }}" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                <i class="fas fa-warehouse w-5"></i>
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
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold font-space gradient-text">Inventory Details</h1>
                    <p class="mt-2 text-sm text-gray-400">Detailed view of inventory item</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('inventory.edit', $inventory) }}" class="btn-primary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Inventory
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn-secondary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Inventory
                    </a>
                </div>
            </div>
        </div>

        <!-- Product Information -->
        <div class="glass-card p-6 mb-6">
            <div class="flex items-center space-x-4 mb-6">
                <div class="h-16 w-16 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                    <span class="text-2xl font-bold text-white">{{ substr($inventory->product->name ?? 'N/A', 0, 2) }}</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $inventory->product->name ?? 'N/A' }}</h2>
                    <p class="text-gray-400">SKU: {{ $inventory->product->sku ?? 'N/A' }}</p>
                    <p class="text-gray-400">Category: {{ $inventory->product->category->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Quantity On Hand</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($inventory->quantity_on_hand ?? 0) }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-boxes text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Available</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($inventory->quantity_available ?? 0) }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Reserved</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($inventory->quantity_reserved ?? 0) }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Value</p>
                        <p class="text-2xl font-bold text-white">${{ number_format($inventoryValue ?? 0, 2) }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Warehouse Information -->
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-warehouse mr-2 text-blue-400"></i>
                    Warehouse Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Warehouse:</span>
                        <span class="text-white font-medium">{{ $inventory->warehouse->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Location:</span>
                        <span class="text-white font-medium">{{ $inventory->location ?? 'Not specified' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Status:</span>
                        <span class="status-badge 
                            @if($stockStatus === 'in_stock') status-in-stock
                            @elseif($stockStatus === 'low_stock') status-low-stock
                            @else status-out-of-stock @endif">
                            {{ ucfirst(str_replace('_', ' ', $stockStatus)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-tag mr-2 text-green-400"></i>
                    Product Details
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Cost Price:</span>
                        <span class="text-white font-medium">${{ number_format($inventory->product->cost_price ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Selling Price:</span>
                        <span class="text-white font-medium">${{ number_format($inventory->product->selling_price ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Reorder Point:</span>
                        <span class="text-white font-medium">{{ number_format($inventory->product->reorder_point ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Batch Number:</span>
                        <span class="text-white font-medium">{{ $inventory->batch_number ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Movements (Placeholder) -->
        <div class="glass-card p-6 mt-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-history mr-2 text-purple-400"></i>
                Recent Movements
            </h3>
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gradient-to-r from-gray-500 to-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-2xl text-white"></i>
                </div>
                <p class="text-gray-400">No recent movements recorded</p>
                <p class="text-sm text-gray-500 mt-2">Inventory movement tracking will be available soon</p>
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