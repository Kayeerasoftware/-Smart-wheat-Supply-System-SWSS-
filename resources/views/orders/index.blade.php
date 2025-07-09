<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SWSS Supplier Dashboard - Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
            height: calc(100vh - 5rem);
            z-index: 40;
            overflow-y: auto;
            width: 16rem;
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
            height: 5rem;
        }

        /* Main content adjustment for fixed elements */
        .main-content {
            margin-left: 16rem;
            margin-top: 5rem;
            min-height: calc(100vh - 5rem);
            padding: 1.5rem;
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                height: 100vh;
                top: 0;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                margin-top: 5rem;
                padding: 1rem;
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

        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: rgb(251, 191, 36);
        }

        .status-confirmed {
            background: rgba(59, 130, 246, 0.2);
            color: rgb(96, 165, 250);
        }

        .status-processing {
            background: rgba(139, 92, 246, 0.2);
            color: rgb(167, 139, 250);
        }

        .status-shipped {
            background: rgba(16, 185, 129, 0.2);
            color: rgb(52, 211, 153);
        }

        .status-delivered {
            background: rgba(34, 197, 94, 0.2);
            color: rgb(74, 222, 128);
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: rgb(248, 113, 113);
        }

        /* Notification badge styling */
        .notification-badge {
            background: var(--secondary-gradient);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.125rem 0.375rem;
            border-radius: 9999px;
            min-width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
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
                setInterval(fetchNotifications, 30000);
                
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
    <aside class="sidebar p-6" id="sidebar">
        <nav class="space-y-2">
            <a href="{{ route('supplier.dashboard') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="{{ route('inventory.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-seedling w-5"></i>
                <span class="font-medium">Inventory</span>
            </a>
            <a href="{{ route('orders.index') }}" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
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
    <main class="main-content">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold font-space gradient-text">Order Management</h1>
                    <p class="mt-2 text-sm text-gray-400">Track and manage all your customer orders</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('orders.create') }}" class="btn-primary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
                        <i class="fas fa-plus mr-2"></i>
                        New Order
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card p-6 rounded-2xl text-center float-animation">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shopping-cart text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold font-space mb-2">{{ $orders->total() ?? 0 }}</h3>
                <p class="text-gray-400 mb-2">Total Orders</p>
            </div>

            <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.2s">
                <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold font-space mb-2">{{ $pendingCount ?? 0 }}</h3>
                <p class="text-gray-400 mb-2">Pending</p>
            </div>

            <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.4s">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-truck text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold font-space mb-2">{{ $shippedCount ?? 0 }}</h3>
                <p class="text-gray-400 mb-2">In Transit</p>
            </div>

            <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.6s">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-dollar-sign text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold font-space mb-2">${{ number_format($totalRevenue ?? 0) }}</h3>
                <p class="text-gray-400 mb-2">Revenue</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="glass-card p-6 rounded-2xl mb-6">
            <h3 class="text-lg font-medium font-space mb-4">Filters</h3>
            <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-300 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="form-input-glass w-full rounded-lg px-3 py-2" 
                           placeholder="Order number or customer">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                    <select name="status" id="status" class="form-select-glass w-full rounded-lg px-3 py-2">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-300 mb-1">From Date</label>
                    <input type="date" name="date_from" id="date_from" 
                           class="form-input-glass w-full rounded-lg px-3 py-2" 
                           value="{{ request('date_from') }}">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-300 mb-1">To Date</label>
                    <input type="date" name="date_to" id="date_to" 
                           class="form-input-glass w-full rounded-lg px-3 py-2" 
                           value="{{ request('date_to') }}">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full px-6 py-2 rounded-xl font-semibold text-sm text-white uppercase tracking-wider hover:transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-search mr-2"></i>
                        Apply
                    </button>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="glass-card rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders ?? [] as $order)
                        <tr class="table-row border-b border-gray-800">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">#</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-white">{{ $order->order_number ?? 'ORD-' . $order->id }}</div>
                                        <div class="text-sm text-gray-400">Priority: {{ $order->priority ?? 'Medium' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-white">{{ $order->customer_name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-400">{{ $order->customer_email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-white">{{ $order->product_name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-400">{{ $order->quantity ?? 'N/A' }} kg</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-white">${{ number_format($order->total_amount ?? 0, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="status-badge status-{{ strtolower($order->status ?? 'pending') }}">
                                    <i class="fas fa-{{ $order->status == 'pending' ? 'clock' : ($order->status == 'shipped' ? 'truck' : ($order->status == 'delivered' ? 'check-double' : 'circle')) }}"></i>
                                    {{ ucfirst($order->status ?? 'Pending') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-white">{{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</div>
                                <div class="text-sm text-gray-400">Expected: {{ $order->expected_delivery ? $order->expected_delivery->format('M d, Y') : 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-3">
                                    <a href="{{ route('orders.show', $order->id) }}" class="text-blue-400 hover:text-blue-300 transition-colors" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('orders.edit', $order->id) }}" class="text-green-400 hover:text-green-300 transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="text-purple-400 hover:text-purple-300 transition-colors" title="Track">
                                        <i class="fas fa-truck"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No orders found</p>
                                    <p class="text-sm">Start by creating your first order</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($orders) && $orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-400">
                        Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() ?? 0 }} results
                    </div>
                    <div class="flex space-x-2">
                        @if($orders->onFirstPage())
                            <span class="px-3 py-1 text-sm text-gray-500">Previous</span>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}" class="px-3 py-1 text-sm text-gray-400 hover:text-white transition-colors">Previous</a>
                        @endif

                        @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                            @if($page == $orders->currentPage())
                                <span class="px-3 py-1 text-sm bg-blue-500 text-white rounded">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1 text-sm text-gray-400 hover:text-white transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}" class="px-3 py-1 text-sm text-gray-400 hover:text-white transition-colors">Next</a>
                        @else
                            <span class="px-3 py-1 text-sm text-gray-500">Next</span>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </main>

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });

            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            });
        }

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