<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SWSS Supplier Dashboard')</title>
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
            background: var(--accent-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-secondary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--dark-gradient);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .btn-secondary:hover::before {
            left: 0;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .status-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .feature-item.available {
            border-color: rgba(16, 185, 129, 0.3);
        }

        .feature-item.locked {
            border-color: rgba(156, 163, 175, 0.3);
            opacity: 0.6;
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

        .main-content {
            margin-left: 16rem; /* 256px for sidebar width */
            margin-top: 5rem; /* 80px for navigation height */
            min-height: calc(100vh - 5rem);
        }

        .logo-pulse {
            animation: logoPulse 2s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
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
            <div class="relative" x-data="{ 
                open: false, 
                notifications: [], 
                unreadCount: 0,
                
                loadNotifications() {
                    fetch('/notifications/unread-count')
                        .then(response => response.json())
                        .then(data => {
                            this.unreadCount = data.unread_count;
                        });
                },
                
                loadNotificationList() {
                    fetch('/notifications')
                        .then(response => response.json())
                        .then(data => {
                            this.notifications = data.notifications;
                            this.unreadCount = data.unread_count;
                        });
                },
                
                markAsRead(id) {
                    fetch(`/notifications/${id}/read`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        this.loadNotificationList();
                    });
                },
                
                markAllAsRead() {
                    fetch('/notifications/mark-all-read', {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        this.loadNotificationList();
                    });
                }
            }" x-init="
                loadNotifications();
                loadNotificationList();
                setInterval(() => { loadNotifications(); loadNotificationList(); }, 30000);
            ">
                <button @click="open = !open" class="relative">
                    <i class="fas fa-bell text-gray-300 text-xl cursor-pointer hover:text-white transition-colors"></i>
                    <span x-show="unreadCount > 0" x-text="unreadCount" 
                          class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-red-500 flex items-center justify-center text-xs font-bold"
                          style="display: none;"></span>
                </button>
                
                <!-- Notification Dropdown -->
                <div x-show="open" @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="absolute right-0 mt-2 w-80 bg-gray-800 rounded-lg shadow-xl border border-gray-700 z-50"
                     style="display: none;">
                    
                    <!-- Header -->
                    <div class="px-4 py-3 border-b border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-white">Notifications</h3>
                            <button @click="markAllAsRead()" class="text-xs text-blue-400 hover:text-blue-300">
                                Mark all as read
                            </button>
                        </div>
                    </div>
                    
                    <!-- Notifications List -->
                    <div class="max-h-64 overflow-y-auto">
                        <template x-for="notification in notifications" :key="notification.id">
                            <div class="px-4 py-3 hover:bg-gray-700 transition-colors cursor-pointer border-b border-gray-700 last:border-b-0"
                                 :class="{ 'bg-blue-900/20': !notification.read_at }"
                                 @click="markAsRead(notification.id)">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <i :class="notification.icon" 
                                           class="text-lg"
                                           :class="{
                                               'text-blue-400': notification.color === 'blue',
                                               'text-green-400': notification.color === 'green',
                                               'text-yellow-400': notification.color === 'yellow',
                                               'text-red-400': notification.color === 'red'
                                           }"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-white" x-text="notification.title"></p>
                                        <p class="text-xs text-gray-400 mt-1" x-text="notification.message"></p>
                                        <p class="text-xs text-gray-500 mt-1" x-text="notification.created_at"></p>
                                    </div>
                                    <div x-show="!notification.read_at" class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="notifications.length === 0" class="px-4 py-8 text-center">
                            <i class="fas fa-bell-slash text-gray-500 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-400">No notifications</p>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="px-4 py-2 border-t border-gray-700">
                        <a href="#" class="text-xs text-blue-400 hover:text-blue-300">View all notifications</a>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm font-semibold">{{ auth()->user()->name ?? 'Supplier' }}</p>
                    <p class="text-xs text-gray-400">Supplier Portal</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 2)) }}</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar w-64 p-6" id="sidebar">
        <nav class="space-y-2">
            <a href="{{ route('supplier.dashboard') }}" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white relative">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span class="font-medium">Dashboard</span>
                <span class="absolute -top-1 -right-1 w-3 h-3 bg-blue-400 rounded-full"></span>
            </a>
            <a href="{{ route('chat.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-comments w-5"></i>
                <span class="font-medium">Chat with Admin</span>
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
        @yield('content')
    </main>

    <script>
        // Mobile sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });
    </script>
</body>
</html> 