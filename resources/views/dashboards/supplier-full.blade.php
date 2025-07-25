<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SWSS Supplier Dashboard</title>
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
            --success-gradient: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            --warning-gradient: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            --danger-gradient: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
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

        .btn-success {
            background: var(--success-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-warning {
            background: var(--warning-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-danger {
            background: var(--danger-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
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
            transition: width 0.3s ease;
        }

        .status-badge {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .notification-dot {
            background: var(--secondary-gradient);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .notification-badge {
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .metric-card {
            background: var(--card-gradient);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .alert-card {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .success-card {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
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

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .tab-button {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #9ca3af;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
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
            <div class="relative" x-data="{ open: false, notifications: [], unreadCount: 0 }" x-init="
                fetchNotifications();
                setInterval(fetchNotifications, 30000); // Refresh every 30 seconds
                
                function fetchNotifications() {
                    fetch('/notifications')
                        .then(response => response.json())
                        .then(data => {
                            notifications = data.notifications;
                            unreadCount = data.unread_count;
                        })
                        .catch(error => console.error('Error fetching notifications:', error));
                }
                
                function markAsRead(id) {
                    fetch(`/notifications/${id}/read`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(() => fetchNotifications())
                    .catch(error => console.error('Error marking notification as read:', error));
                }
                
                function markAllAsRead() {
                    fetch('/notifications/mark-all-read', {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(() => fetchNotifications())
                    .catch(error => console.error('Error marking all notifications as read:', error));
                }
            ">
                <button @click="open = !open" class="relative p-2 text-gray-300 hover:text-white transition-colors">
                    <i class="fas fa-bell text-xl"></i>
                    <span x-show="unreadCount > 0" x-text="unreadCount" 
                          class="notification-badge absolute -top-1 -right-1">
                    </span>
                </button>
                
                <!-- Notifications Dropdown -->
                <div x-show="open" @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-72 bg-gray-900 border border-gray-700 rounded-lg shadow-xl z-50 max-h-80 overflow-hidden">
                    
                    <!-- Header -->
                    <div class="px-3 py-2 border-b border-gray-700 bg-gray-800">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-white">Notifications</h3>
                            <button @click="markAllAsRead()" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                                Mark all read
                            </button>
                        </div>
                    </div>
                    
                    <!-- Notifications List -->
                    <div class="max-h-60 overflow-y-auto bg-gray-900">
                        <template x-if="notifications.length === 0">
                            <div class="px-3 py-4">
                                <div class="text-center text-gray-400 text-sm">
                                    <i class="fas fa-check-circle text-green-400 text-base mb-1"></i>
                                    <p>All caught up!</p>
                                    <p class="text-xs mt-1">No new notifications</p>
                                </div>
                            </div>
                        </template>
                        
                        <template x-for="notification in notifications" :key="notification.id">
                            <div class="px-3 py-2 hover:bg-gray-800 transition-colors cursor-pointer border-b border-gray-800 last:border-b-0"
                                 :class="{ 'bg-blue-900/20 border-l-2 border-l-blue-400': !notification.read_at }"
                                 @click="markAsRead(notification.id)">
                                <div class="flex items-start space-x-2">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <i :class="notification.icon" 
                                           :class="{
                                               'text-blue-400': notification.color === 'blue',
                                               'text-green-400': notification.color === 'green',
                                               'text-yellow-400': notification.color === 'yellow',
                                               'text-red-400': notification.color === 'red',
                                               'text-purple-400': notification.color === 'purple',
                                               'text-gray-400': notification.color === 'gray'
                                           }"
                                           class="text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs font-medium text-white truncate" x-text="notification.title"></p>
                                            <div x-show="!notification.read_at" class="flex-shrink-0 ml-1">
                                                <div class="w-1.5 h-1.5 bg-blue-400 rounded-full"></div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-300 mt-0.5 line-clamp-1" x-text="notification.message"></p>
                                        <p class="text-xs text-gray-500 mt-0.5" x-text="notification.created_at"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Footer -->
                    <div class="px-3 py-2 border-t border-gray-700 bg-gray-800">
                        <div class="text-center">
                            <a href="/notifications" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                                View all notifications
                            </a>
                        </div>
                    </div>
                </div>
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
            <a href="{{ route('supplier.dashboard') }}" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="{{ route('inventory.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
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
    <main class="main-content p-6" x-data="{ activeTab: 'overview' }">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold font-space mb-2 gradient-text">Supplier Dashboard</h1>
            <p class="text-xl text-gray-300">Full access granted. Welcome to your complete supplier portal!</p>
        </div>

        <!-- Success Alert -->
        <div class="glass-card p-6 mb-8 border-l-4 border-green-400">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-3xl text-green-400"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-white mb-1">Full Access Granted!</h3>
                    <p class="text-gray-300">Congratulations! Your application has been approved. You now have full access to all supplier features.</p>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="flex space-x-2 mb-6">
            <button @click="activeTab = 'overview'" 
                    :class="activeTab === 'overview' ? 'tab-button active' : 'tab-button'"
                    class="px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-chart-pie mr-2"></i>
                Overview
            </button>
            <button @click="activeTab = 'analytics'" 
                    :class="activeTab === 'analytics' ? 'tab-button active' : 'tab-button'"
                    class="px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-chart-line mr-2"></i>
                Analytics
            </button>
            <button @click="activeTab = 'orders'" 
                    :class="activeTab === 'orders' ? 'tab-button active' : 'tab-button'"
                    class="px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-shopping-cart mr-2"></i>
                Orders
            </button>
            <button @click="activeTab = 'inventory'" 
                    :class="activeTab === 'inventory' ? 'tab-button active' : 'tab-button'"
                    class="px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-boxes mr-2"></i>
                Inventory
            </button>
            <button @click="activeTab = 'performance'" 
                    :class="activeTab === 'performance' ? 'tab-button active' : 'tab-button'"
                    class="px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-trophy mr-2"></i>
                Performance
            </button>
        </div>

        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" class="tab-content active">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-boxes text-xl text-white"></i>
                        </div>
                        <span class="status-badge">
                            <i class="fas fa-check"></i>
                            Active
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Total Inventory</h3>
                    <p class="text-2xl font-bold text-blue-400 mb-1">{{ number_format($totalInventory ?? 0) }}</p>
                    <p class="text-gray-300 text-sm">Items in stock</p>
                </div>

                <div class="stat-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-xl text-white"></i>
                        </div>
                        <span class="status-badge">
                            <i class="fas fa-check"></i>
                            Active
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Inventory Value</h3>
                    <p class="text-2xl font-bold text-green-400 mb-1">${{ number_format($totalInventoryValue ?? 0) }}</p>
                    <p class="text-gray-300 text-sm">Total value</p>
                </div>

                <div class="stat-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-xl text-white"></i>
                        </div>
                        <span class="status-badge">
                            <i class="fas fa-check"></i>
                            Active
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Active Orders</h3>
                    <p class="text-2xl font-bold text-purple-400 mb-1">{{ $activeOrders ?? 0 }}</p>
                    <p class="text-gray-300 text-sm">Pending orders</p>
                </div>

                <div class="stat-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-xl text-white"></i>
                        </div>
                        <span class="status-badge">
                            <i class="fas fa-check"></i>
                            Active
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Low Stock Items</h3>
                    <p class="text-2xl font-bold text-orange-400 mb-1">{{ $lowStockItems ?? 0 }}</p>
                    <p class="text-gray-300 text-sm">Need attention</p>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="metric-card">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-white">Order Fulfillment Rate</h4>
                        <i class="fas fa-truck text-blue-400 text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-green-400 mb-2">98.5%</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 98.5%"></div>
                    </div>
                    <p class="text-sm text-gray-400 mt-2">+2.3% from last month</p>
                </div>

                <div class="metric-card">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-white">Customer Satisfaction</h4>
                        <i class="fas fa-star text-yellow-400 text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-yellow-400 mb-2">4.8/5</div>
                    <div class="flex space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= 4 ? 'text-yellow-400' : ($i == 5 ? 'text-yellow-400' : 'text-gray-600') }}"></i>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-400 mt-2">Based on 127 reviews</p>
                </div>

                <div class="metric-card">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-white">Inventory Turnover</h4>
                        <i class="fas fa-sync-alt text-purple-400 text-xl"></i>
                    </div>
                    <div class="text-3xl font-bold text-purple-400 mb-2">12.4x</div>
                    <p class="text-sm text-gray-400">Annual turnover rate</p>
                    <p class="text-sm text-green-400 mt-2">+1.2x from last year</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="glass-card p-6 mb-8">
                <h3 class="text-xl font-semibold text-white mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('inventory.create') }}" class="btn-primary px-4 py-3 rounded-xl text-white text-center hover:scale-105 transition-transform">
                        <i class="fas fa-plus text-lg mb-2"></i>
                        <p class="font-medium">Add Inventory</p>
                    </a>
                    
                    <a href="{{ route('orders.create') }}" class="btn-primary px-4 py-3 rounded-xl text-white text-center hover:scale-105 transition-transform">
                        <i class="fas fa-shopping-cart text-lg mb-2"></i>
                        <p class="font-medium">Create Order</p>
                    </a>
                    
                    <a href="{{ route('reports.index') }}" class="btn-primary px-4 py-3 rounded-xl text-white text-center hover:scale-105 transition-transform">
                        <i class="fas fa-chart-bar text-lg mb-2"></i>
                        <p class="font-medium">View Reports</p>
                    </a>
                    
                    <a href="{{ route('chat.index') }}" class="btn-primary px-4 py-3 rounded-xl text-white text-center hover:scale-105 transition-transform">
                        <i class="fas fa-comments text-lg mb-2"></i>
                        <p class="font-medium">Chat Support</p>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="glass-card p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    @if(isset($recentActivity) && $recentActivity->count() > 0)
                        @foreach($recentActivity as $activity)
                            <div class="activity-item p-3 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-500/20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-info-circle text-blue-400"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-white">{{ $activity->description ?? 'Activity recorded' }}</p>
                                        <p class="text-xs text-gray-400">{{ $activity->created_at ? $activity->created_at->diffForHumans() : 'Recently' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-400 py-8">
                            <i class="fas fa-chart-line text-3xl mb-3"></i>
                            <p>No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Analytics Tab -->
        <div x-show="activeTab === 'analytics'" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Inventory Trend Chart -->
                <div class="glass-card p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Inventory Trend (Last 30 Days)</h3>
                    <div class="chart-container p-4">
                        <canvas id="inventoryTrendChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="glass-card p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Revenue Analytics</h3>
                    <div class="chart-container p-4">
                        <canvas id="revenueChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Sales Performance -->
            <div class="glass-card p-6 mb-6">
                <h3 class="text-xl font-semibold text-white mb-4">Sales Performance</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-blue-500/10 rounded-lg">
                        <div class="text-2xl font-bold text-blue-400">$45,230</div>
                        <div class="text-sm text-gray-300">This Month</div>
                        <div class="text-xs text-green-400">+12.5%</div>
                    </div>
                    <div class="text-center p-4 bg-green-500/10 rounded-lg">
                        <div class="text-2xl font-bold text-green-400">$189,450</div>
                        <div class="text-sm text-gray-300">This Quarter</div>
                        <div class="text-xs text-green-400">+8.3%</div>
                    </div>
                    <div class="text-center p-4 bg-purple-500/10 rounded-lg">
                        <div class="text-2xl font-bold text-purple-400">$723,890</div>
                        <div class="text-sm text-gray-300">This Year</div>
                        <div class="text-xs text-green-400">+15.7%</div>
                    </div>
                    <div class="text-center p-4 bg-orange-500/10 rounded-lg">
                        <div class="text-2xl font-bold text-orange-400">156</div>
                        <div class="text-sm text-gray-300">Orders This Month</div>
                        <div class="text-xs text-green-400">+5.2%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Tab -->
        <div x-show="activeTab === 'orders'" class="tab-content">
            <div class="glass-card p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-white">Recent Orders</h3>
                    <a href="{{ route('orders.index') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <!-- Sample Orders Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">Order ID</th>
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">Customer</th>
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">Amount</th>
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">Status</th>
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">Date</th>
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-700/50 hover:bg-white/5 transition-colors">
                                <td class="py-3 px-4 text-white">#ORD-001</td>
                                <td class="py-3 px-4 text-gray-300">Wheat Distributors Inc.</td>
                                <td class="py-3 px-4 text-gray-300">$2,450.00</td>
                                <td class="py-3 px-4">
                                    <span class="bg-green-500/20 text-green-300 px-2 py-1 rounded-full text-xs">Completed</span>
                                </td>
                                <td class="py-3 px-4 text-gray-300">2025-06-28</td>
                                <td class="py-3 px-4">
                                    <button class="text-blue-400 hover:text-blue-300 text-xs">View</button>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-700/50 hover:bg-white/5 transition-colors">
                                <td class="py-3 px-4 text-white">#ORD-002</td>
                                <td class="py-3 px-4 text-gray-300">Bakery Supply Co.</td>
                                <td class="py-3 px-4 text-gray-300">$1,890.00</td>
                                <td class="py-3 px-4">
                                    <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded-full text-xs">Processing</span>
                                </td>
                                <td class="py-3 px-4 text-gray-300">2025-06-29</td>
                                <td class="py-3 px-4">
                                    <button class="text-blue-400 hover:text-blue-300 text-xs">View</button>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-700/50 hover:bg-white/5 transition-colors">
                                <td class="py-3 px-4 text-white">#ORD-003</td>
                                <td class="py-3 px-4 text-gray-300">Grain Market LLC</td>
                                <td class="py-3 px-4 text-gray-300">$3,120.00</td>
                                <td class="py-3 px-4">
                                    <span class="bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded-full text-xs">Pending</span>
                                </td>
                                <td class="py-3 px-4 text-gray-300">2025-06-29</td>
                                <td class="py-3 px-4">
                                    <button class="text-blue-400 hover:text-blue-300 text-xs">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Inventory Tab -->
        <div x-show="activeTab === 'inventory'" class="tab-content">
            <!-- Inventory Overview -->
            @if(isset($supplierInventory) && $supplierInventory->count() > 0)
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-white">Recent Inventory Items</h3>
                    <a href="{{ route('inventory.index') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">Product</th>
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">SKU</th>
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">Quantity</th>
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">Warehouse</th>
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">Status</th>
                                <th class="text-left py-3 px-4 text-gray-300 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supplierInventory->take(5) as $inventory)
                            <tr class="border-b border-gray-700/50 hover:bg-white/5 transition-colors">
                                <td class="py-3 px-4 text-white">{{ $inventory->product->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4 text-gray-300">{{ $inventory->product->sku ?? 'N/A' }}</td>
                                <td class="py-3 px-4 text-gray-300">{{ number_format($inventory->quantity_on_hand ?? 0) }}</td>
                                <td class="py-3 px-4 text-gray-300">{{ $inventory->warehouse->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4">
                                    @if(($inventory->quantity_available ?? 0) <= 0)
                                        <span class="bg-red-500/20 text-red-300 px-2 py-1 rounded-full text-xs">Out of Stock</span>
                                    @elseif(($inventory->quantity_available ?? 0) <= 10)
                                        <span class="bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded-full text-xs">Low Stock</span>
                                    @else
                                        <span class="bg-green-500/20 text-green-300 px-2 py-1 rounded-full text-xs">In Stock</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <button class="text-blue-400 hover:text-blue-300 text-xs">Edit</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Low Stock Alerts -->
            @if(isset($lowStockItems) && $lowStockItems > 0)
            <div class="alert-card mt-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-xl mr-3"></i>
                    <div>
                        <h4 class="text-white font-semibold">Low Stock Alert</h4>
                        <p class="text-gray-300 text-sm">You have {{ $lowStockItems }} items that need restocking</p>
                    </div>
                    <a href="{{ route('inventory.index') }}" class="btn-warning px-4 py-2 rounded-lg text-white text-sm ml-auto">
                        View Details
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Performance Tab -->
        <div x-show="activeTab === 'performance'" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Supplier Score -->
                <div class="glass-card p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Supplier Performance Score</h3>
                    <div class="text-center">
                        <div class="text-6xl font-bold text-green-400 mb-4">87.7</div>
                        <div class="text-lg text-gray-300 mb-4">Excellent Performance</div>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-blue-400">85</div>
                                <div class="text-xs text-gray-400">Financial</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-400">90</div>
                                <div class="text-xs text-gray-400">Reputation</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-orange-400">88</div>
                                <div class="text-xs text-gray-400">Compliance</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quality Metrics -->
                <div class="glass-card p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Quality Metrics</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-300">Product Quality</span>
                                <span class="text-green-400 font-semibold">98.2%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 98.2%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-300">Delivery Accuracy</span>
                                <span class="text-green-400 font-semibold">96.8%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 96.8%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-300">Response Time</span>
                                <span class="text-yellow-400 font-semibold">92.1%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 92.1%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Achievements -->
            <div class="glass-card p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Recent Achievements</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-yellow-500/10 rounded-lg border border-yellow-500/20">
                        <i class="fas fa-trophy text-yellow-400 text-3xl mb-2"></i>
                        <h4 class="text-white font-semibold">Top Performer</h4>
                        <p class="text-gray-300 text-sm">Q2 2025</p>
                    </div>
                    <div class="text-center p-4 bg-green-500/10 rounded-lg border border-green-500/20">
                        <i class="fas fa-award text-green-400 text-3xl mb-2"></i>
                        <h4 class="text-white font-semibold">Quality Excellence</h4>
                        <p class="text-gray-300 text-sm">6 months running</p>
                    </div>
                    <div class="text-center p-4 bg-blue-500/10 rounded-lg border border-blue-500/20">
                        <i class="fas fa-star text-blue-400 text-3xl mb-2"></i>
                        <h4 class="text-white font-semibold">Customer Choice</h4>
                        <p class="text-gray-300 text-sm">Highest rated</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Initialize inventory trend chart
        const ctx = document.getElementById('inventoryTrendChart').getContext('2d');
        const inventoryTrendData = @json($inventoryTrendData ?? []);
        
        if (inventoryTrendData.length > 0) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: inventoryTrendData.map(item => item.date),
                    datasets: [{
                        label: 'Total Quantity',
                        data: inventoryTrendData.map(item => item.total_quantity),
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
                            labels: {
                                color: '#ffffff'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#9ca3af'
                            },
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            }
                        },
                        y: {
                            ticks: {
                                color: '#9ca3af'
                            },
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            }
                        }
                    }
                }
            });
        }

        // Initialize revenue chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed Orders', 'Pending Orders', 'Cancelled Orders'],
                datasets: [{
                    data: [75, 20, 5],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgba(34, 197, 94, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff'
                        }
                    }
                }
            }
        });

        // Real-time updates simulation
        function updateMetrics() {
            // Simulate real-time metric updates
            const metrics = document.querySelectorAll('.metric-card .text-3xl');
            metrics.forEach(metric => {
                const currentValue = parseFloat(metric.textContent);
                const variation = (Math.random() - 0.5) * 2; // -1 to 1
                const newValue = Math.max(0, currentValue + variation);
                
                if (metric.textContent.includes('%')) {
                    metric.textContent = newValue.toFixed(1) + '%';
                } else if (metric.textContent.includes('x')) {
                    metric.textContent = newValue.toFixed(1) + 'x';
                } else if (metric.textContent.includes('/')) {
                    metric.textContent = newValue.toFixed(1) + '/5';
                }
            });
        }

        // Update metrics every 30 seconds
        setInterval(updateMetrics, 30000);

        // Add smooth scrolling for tab navigation
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
            });
        });

        // Add hover effects for stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 p-4 rounded-lg text-white z-50 transition-all duration-300 transform translate-x-full`;
            
            switch(type) {
                case 'success':
                    notification.style.background = 'linear-gradient(135deg, #4ade80 0%, #22c55e 100%)';
                    break;
                case 'warning':
                    notification.style.background = 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)';
                    break;
                case 'error':
                    notification.style.background = 'linear-gradient(135deg, #f87171 0%, #ef4444 100%)';
                    break;
                default:
                    notification.style.background = 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)';
            }
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(full)';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, 5000);
        }

        // Add click handlers for quick actions
        document.querySelectorAll('.btn-primary').forEach(button => {
            button.addEventListener('click', function(e) {
                // Add ripple effect
                const ripple = document.createElement('span');
                ripple.className = 'absolute inset-0 bg-white/20 rounded-xl transform scale-0 transition-transform duration-300';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.style.transform = 'scale(1)';
                }, 10);
                
                setTimeout(() => {
                    ripple.remove();
                }, 300);
            });
        });

        // Performance monitoring
        let performanceData = {
            pageLoadTime: performance.now(),
            interactions: 0
        };

        // Track user interactions
        document.addEventListener('click', () => {
            performanceData.interactions++;
        });

        // Log performance data when user leaves
        window.addEventListener('beforeunload', () => {
            const sessionDuration = performance.now() - performanceData.pageLoadTime;
            console.log('Session Duration:', Math.round(sessionDuration / 1000), 'seconds');
            console.log('Total Interactions:', performanceData.interactions);
        });

        // Mobile sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                });
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    if (window.innerWidth <= 768) {
                        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                            sidebar.classList.remove('open');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html> 