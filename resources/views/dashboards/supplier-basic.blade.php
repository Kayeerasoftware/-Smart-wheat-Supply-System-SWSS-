<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SWSS Supplier Dashboard - Basic Access</title>
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

        .notification-dot {
            background: var(--secondary-gradient);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
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
                    <span x-show="unreadCount > 0 || {{ $scheduledVisits->count() }} > 0" 
                          x-text="unreadCount + {{ $scheduledVisits->count() }}" 
                          class="notification-dot absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold"
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
                                Mark all read
                            </button>
                        </div>
                    </div>
                    
                    <!-- Notifications List -->
                    <div class="max-h-64 overflow-y-auto">
                        <!-- Facility Visit Alerts -->
                        @if($scheduledVisits->count() > 0)
                            <div class="px-4 py-3 border-b border-gray-700 bg-blue-900/20">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-semibold text-blue-300">Facility Visits</h4>
                                    <span class="text-xs text-blue-400">{{ $scheduledVisits->count() }} scheduled</span>
                                </div>
                                @foreach($scheduledVisits->take(2) as $index => $visit)
                                    @php
                                        $scheduledAt = $visit->scheduled_at ? \Carbon\Carbon::parse($visit->scheduled_at) : null;
                                        $timeUntilVisit = $scheduledAt ? $scheduledAt->diffForHumans() : 'Date TBD';
                                    @endphp
                                    <div class="flex items-start space-x-3 py-2 {{ $index > 0 ? 'border-t border-gray-700/50' : '' }} cursor-pointer hover:bg-blue-800/30 transition-colors"
                                         onclick="showFacilityVisitDetails(); open = false;">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-calendar-check text-blue-400 text-lg"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-white">
                                                Visit #{{ $index + 1 }} - {{ $scheduledAt ? $scheduledAt->format('M d, Y') : 'Date TBD' }}
                                            </p>
                                            <p class="text-xs text-blue-300 mt-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $scheduledAt ? $scheduledAt->format('g:i A') : 'Time TBD' }}
                                            </p>
                                            <p class="text-xs text-yellow-300 mt-1">
                                                <i class="fas fa-hourglass-half mr-1"></i>
                                                {{ $timeUntilVisit }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($scheduledVisits->count() > 2)
                                    <div class="text-center pt-2 cursor-pointer hover:bg-blue-800/30 transition-colors py-1"
                                         onclick="showFacilityVisitDetails(); open = false;">
                                        <p class="text-xs text-blue-400">
                                            +{{ $scheduledVisits->count() - 2 }} more visits
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Regular Notifications -->
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
                        
                        <div x-show="notifications.length === 0 && {{ $scheduledVisits->count() }} === 0" class="px-4 py-8 text-center">
                            <i class="fas fa-bell-slash text-gray-500 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-400">No notifications</p>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="px-4 py-2 border-t border-gray-700">
                        <div class="flex justify-between items-center">
                            @if($scheduledVisits->count() > 0)
                                <button onclick="showFacilityVisitDetails(); open = false;" 
                                        class="text-xs text-blue-400 hover:text-blue-300">
                                    View all visits
                                </button>
                            @endif
                            <a href="#" class="text-xs text-gray-400 hover:text-gray-300">
                                View all notifications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm font-semibold">{{ Auth::user()->username }}</p>
                    <p class="text-xs text-gray-400">Basic Access</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ strtoupper(substr(Auth::user()->username, 0, 2)) }}</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar w-64 p-6" id="sidebar">
        <nav class="space-y-2">
            <a href="#" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="{{ route('chat.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 relative">
                <i class="fas fa-comments w-5"></i>
                <span class="font-medium">Chat with Admin</span>
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
            <h1 class="text-4xl font-bold font-space mb-2">Supplier Dashboard</h1>
            <p class="text-xl text-gray-300">{{ $statusMessage }}</p>
        </div>

        <!-- Success Alert -->
        <div class="glass-card p-6 mb-8 border-l-4 border-green-400">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-3xl text-green-400"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-white mb-1">PDF Validation Successful!</h3>
                    <p class="text-gray-300">Your PDF document has been validated successfully. You now have basic access to the system while awaiting facility visit approval.</p>
                </div>
            </div>
        </div>

        <!-- Limited Access Notice -->
        <div class="glass-card p-6 mb-8 border-l-4 border-blue-400">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-3xl text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-white mb-1">Limited Access Mode</h3>
                    <p class="text-gray-300">You currently have basic access. Full access to all features will be granted after facility visit approval.</p>
                </div>
            </div>
        </div>

        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-check text-xl text-white"></i>
                    </div>
                    <span class="status-badge">
                        <i class="fas fa-check"></i>
                        Validated
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">PDF Status</h3>
                <p class="text-gray-300 text-sm">Document approved and validated</p>
            </div>

            <div class="stat-card p-6 cursor-pointer hover:bg-opacity-20 transition-all duration-300" 
                 onclick="showFacilityVisitDetails()">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-building text-xl text-white"></i>
                    </div>
                    @if($scheduledVisits->count() > 0)
                        <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-xs font-semibold">
                            Scheduled
                        </span>
                    @else
                        <span class="bg-yellow-500/20 text-yellow-300 px-3 py-1 rounded-full text-xs font-semibold">
                            Pending
                        </span>
                    @endif
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Facility Visit</h3>
                @if($scheduledVisits->count() > 0)
                    @php
                        $nextVisit = $scheduledVisits->sortBy('scheduled_at')->first();
                        $scheduledAt = $nextVisit->scheduled_at ? \Carbon\Carbon::parse($nextVisit->scheduled_at) : null;
                    @endphp
                    <p class="text-blue-300 text-sm mb-1">
                        <i class="fas fa-calendar-check mr-1"></i>
                        {{ $scheduledAt ? $scheduledAt->format('M d, Y') : 'Date TBD' }}
                    </p>
                    <p class="text-gray-300 text-sm">
                        <i class="fas fa-clock mr-1"></i>
                        {{ $scheduledAt ? $scheduledAt->format('g:i A') : 'Time TBD' }}
                    </p>
                    @if($scheduledVisits->count() > 1)
                        <p class="text-xs text-purple-300 mt-1">
                            <i class="fas fa-list mr-1"></i>
                            {{ $scheduledVisits->count() }} visits scheduled
                        </p>
                    @endif
                    <p class="text-xs text-blue-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Click for details
                    </p>
                @else
                    <p class="text-gray-300 text-sm">Awaiting scheduling</p>
                    <p class="text-xs text-yellow-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Admin will schedule soon
                    </p>
                @endif
            </div>

            <div class="stat-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-unlock text-xl text-white"></i>
                    </div>
                    <span class="bg-gray-500/20 text-gray-300 px-3 py-1 rounded-full text-xs font-semibold">
                        Locked
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Full Access</h3>
                <p class="text-gray-300 text-sm">After approval</p>
            </div>
        </div>

        <!-- Facility Visit Information -->
        @if($scheduledVisits->count() > 0)
        <div class="glass-card p-8 mb-8">
            <h2 class="text-2xl font-bold font-space mb-6 gradient-text">Scheduled Facility Visits</h2>

            <div class="space-y-4">
                @foreach($scheduledVisits as $visit)
                @php
                    $scheduledAt = $visit->scheduled_at ? \Carbon\Carbon::parse($visit->scheduled_at) : null;
                    $timeUntilVisit = $scheduledAt ? $scheduledAt->diffForHumans() : 'Date TBD';
                    $daysUntilVisit = $scheduledAt ? $scheduledAt->diffInDays(now()) : 0;
                @endphp
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar-check text-blue-400 text-xl"></i>
                            </div>
                        <div>
                                <h3 class="text-lg font-semibold text-white">Facility Visit Scheduled</h3>
                                <p class="text-blue-300 mb-1">{{ $scheduledAt ? $scheduledAt->format('M d, Y \a\t g:i A') : 'Date TBD' }}</p>
                                <p class="text-sm text-yellow-300 mb-1">
                                    <i class="fas fa-hourglass-half mr-2"></i>
                                    {{ $timeUntilVisit }} @if($daysUntilVisit > 0)({{ $daysUntilVisit }} days)@endif
                                </p>
                            @if($visit->notes)
                                    <p class="text-sm text-gray-300 mt-1">{{ $visit->notes }}</p>
                            @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                <i class="fas fa-clock mr-1"></i>
                            Scheduled
                        </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Features Section -->
        <div class="glass-card p-8 mb-8">
            <h2 class="text-2xl font-bold font-space mb-6 gradient-text">Available Features</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Available Features -->
                <div>
                    <h3 class="text-lg font-semibold text-green-400 mb-4 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        Available Now
                    </h3>
                    <div class="space-y-3">
                        <div class="feature-item available">
                            <div class="flex items-center">
                                <i class="fas fa-eye text-green-400 mr-3"></i>
                                <span class="text-white">View application status</span>
                            </div>
                        </div>
                        <div class="feature-item available">
                            <div class="flex items-center">
                                <i class="fas fa-comments text-green-400 mr-3"></i>
                                <span class="text-white">Chat with admin</span>
                            </div>
                        </div>
                        <div class="feature-item available">
                            <div class="flex items-center">
                                <i class="fas fa-bell text-green-400 mr-3"></i>
                                <span class="text-white">Receive notifications</span>
                            </div>
                        </div>
                        <div class="feature-item available">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-green-400 mr-3"></i>
                                <span class="text-white">View facility visit schedule</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Locked Features -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-400 mb-4 flex items-center">
                        <i class="fas fa-lock mr-2"></i>
                        Available After Approval
                    </h3>
                    <div class="space-y-3">
                        <div class="feature-item locked">
                            <div class="flex items-center">
                                <i class="fas fa-boxes text-gray-400 mr-3"></i>
                                <span class="text-gray-400">Manage inventory</span>
                            </div>
                        </div>
                        <div class="feature-item locked">
                            <div class="flex items-center">
                                <i class="fas fa-shopping-cart text-gray-400 mr-3"></i>
                                <span class="text-gray-400">Process orders</span>
                            </div>
                        </div>
                        <div class="feature-item locked">
                            <div class="flex items-center">
                                <i class="fas fa-chart-bar text-gray-400 mr-3"></i>
                                <span class="text-gray-400">View reports</span>
                            </div>
                        </div>
                        <div class="feature-item locked">
                            <div class="flex items-center">
                                <i class="fas fa-users text-gray-400 mr-3"></i>
                                <span class="text-gray-400">Chat with other users</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        @if($recentActivity->count() > 0)
        <div class="glass-card p-8 mb-8">
            <h2 class="text-2xl font-bold font-space mb-6 gradient-text">Recent Activity</h2>
            <div class="space-y-4">
                @foreach($recentActivity as $activity)
                <div class="activity-item p-4 rounded-lg">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-blue-400 rounded-full mt-2"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                            <p class="text-white">{{ $activity->description }}</p>
                            <p class="text-sm text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Contact Support -->
        <div class="glass-card p-8">
            <h3 class="text-2xl font-bold font-space mb-4 gradient-text">Need Assistance?</h3>
            <p class="text-gray-300 mb-6">If you have questions about the facility visit process or need to reschedule, please contact the admin.</p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('chat.index') }}" class="btn-primary text-white px-6 py-3 rounded-xl font-semibold flex items-center justify-center">
                    <i class="fas fa-comments mr-2"></i>
                Chat with Admin
            </a>
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=support@swss.com&su=SWSS%20Support%20Request&body=Hello%20Admin%20Team%2C%0A%0AI%20need%20assistance%20with%20the%20SWSS%20platform.%0A%0AUser%20ID%3A%20{{ auth()->user()->id ?? 'N/A' }}%0AUsername%3A%20{{ auth()->user()->username ?? auth()->user()->name ?? 'N/A' }}%0A%0APlease%20describe%20your%20issue%20below%3A%0A%0A" 
                   target="_blank"
                   class="btn-secondary text-white px-6 py-3 rounded-xl font-semibold flex items-center justify-center">
                    <i class="fas fa-envelope mr-2"></i>
                    Email Support
                </a>
            </div>
        </div>
    </main>

    <!-- Facility Visit Details Modal -->
    <div id="facilityVisitModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="glass-card rounded-2xl max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white">Facility Visit Details</h3>
                    <button onclick="closeFacilityVisitModal()" class="text-white hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6" id="facilityVisitModalContent">
                @if($scheduledVisits->count() > 0)
                    <div class="mb-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-white">Scheduled Visits ({{ $scheduledVisits->count() }})</h4>
                            <div class="flex space-x-2">
                                <button onclick="previousVisit()" id="prevBtn" class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/30 transition-colors">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <span id="visitCounter" class="px-3 py-1 bg-gray-500/20 text-gray-300 rounded-lg text-sm">
                                    1 of {{ $scheduledVisits->count() }}
                                </span>
                                <button onclick="nextVisit()" id="nextBtn" class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/30 transition-colors">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="max-h-96 overflow-y-auto">
                        @if($scheduledVisits->count() > 1)
                            <div class="flex justify-center mb-4">
                                <div class="flex space-x-2">
                                    @for($i = 0; $i < $scheduledVisits->count(); $i++)
                                        <div class="visit-dot w-2 h-2 rounded-full bg-gray-500 cursor-pointer transition-colors {{ $i === 0 ? 'bg-blue-400' : '' }}" 
                                             onclick="showVisit({{ $i }})" 
                                             data-dot-index="{{ $i }}"></div>
                                    @endfor
                                </div>
                            </div>
                        @endif
                        @foreach($scheduledVisits as $index => $visit)
                            @php
                                $scheduledAt = $visit->scheduled_at ? \Carbon\Carbon::parse($visit->scheduled_at) : null;
                                $timeUntilVisit = $scheduledAt ? $scheduledAt->diffForHumans() : 'Date TBD';
                                $daysUntilVisit = $scheduledAt ? $scheduledAt->diffInDays(now()) : 0;
                            @endphp
                            <div class="visit-item {{ $index === 0 ? '' : 'hidden' }}" data-visit-index="{{ $index }}">
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center">
                                            <i class="fas fa-calendar-check text-blue-400"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-white">Visit #{{ $index + 1 }}</h4>
                                            <p class="text-blue-300">{{ $scheduledAt ? $scheduledAt->format('M d, Y \a\t g:i A') : 'Date TBD' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-300">Time Remaining:</span>
                                                <span class="text-sm text-yellow-300 font-semibold" data-countdown="{{ $scheduledAt ? $scheduledAt->toISOString() : '' }}">
                                                    {{ $timeUntilVisit }}
                                                </span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-300">Days Left:</span>
                                                <span class="text-sm text-blue-300 font-semibold">{{ $daysUntilVisit }} days</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-300">Status:</span>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Scheduled
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($visit->notes)
                                        <div class="bg-gray-500/10 border border-gray-500/20 rounded-lg p-4">
                                            <h5 class="text-sm font-semibold text-white mb-2">Notes from Admin:</h5>
                                            <p class="text-sm text-gray-300">{{ $visit->notes }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                                        <h5 class="text-sm font-semibold text-white mb-2">What to Expect:</h5>
                                        <ul class="text-sm text-gray-300 space-y-1">
                                            <li><i class="fas fa-check text-green-400 mr-2"></i>Facility inspection and assessment</li>
                                            <li><i class="fas fa-check text-green-400 mr-2"></i>Quality control evaluation</li>
                                            <li><i class="fas fa-check text-green-400 mr-2"></i>Capacity and capability review</li>
                                            <li><i class="fas fa-check text-green-400 mr-2"></i>Compliance verification</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-yellow-400 text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-2">No Visit Scheduled Yet</h4>
                        <p class="text-gray-300 text-sm mb-4">Your facility visit will be scheduled by our admin team soon.</p>
                        <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4">
                            <h5 class="text-sm font-semibold text-white mb-2">Next Steps:</h5>
                            <ul class="text-sm text-gray-300 space-y-1">
                                <li><i class="fas fa-arrow-right text-yellow-400 mr-2"></i>Admin will review your application</li>
                                <li><i class="fas fa-arrow-right text-yellow-400 mr-2"></i>Visit will be scheduled</li>
                                <li><i class="fas fa-arrow-right text-yellow-400 mr-2"></i>You'll receive notification</li>
                                <li><i class="fas fa-arrow-right text-yellow-400 mr-2"></i>Visit will appear here</li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });

        // Facility Visit Modal Functions
        function showFacilityVisitDetails() {
            document.getElementById('facilityVisitModal').classList.remove('hidden');
        }

        function closeFacilityVisitModal() {
            document.getElementById('facilityVisitModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('facilityVisitModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFacilityVisitModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFacilityVisitModal();
            }
        });

        // Real-time countdown timer
        function updateCountdown() {
            const countdownElements = document.querySelectorAll('[data-countdown]');
            countdownElements.forEach(element => {
                const targetDate = new Date(element.getAttribute('data-countdown'));
                const now = new Date();
                const diff = targetDate - now;
                
                if (diff > 0) {
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    
                    let countdownText = '';
                    if (days > 0) {
                        countdownText = `${days} day${days > 1 ? 's' : ''}, ${hours} hour${hours > 1 ? 's' : ''}`;
                    } else if (hours > 0) {
                        countdownText = `${hours} hour${hours > 1 ? 's' : ''}, ${minutes} minute${minutes > 1 ? 's' : ''}`;
                    } else {
                        countdownText = `${minutes} minute${minutes > 1 ? 's' : ''}`;
                    }
                    
                    element.textContent = countdownText;
                } else {
                    element.textContent = 'Visit time!';
                    element.classList.add('text-green-400', 'font-bold');
                }
            });
        }

        // Visit navigation
        let currentVisitIndex = 0;
        const totalVisits = {{ $scheduledVisits->count() }};

        function showVisit(index) {
            // Hide all visit items
            document.querySelectorAll('.visit-item').forEach(item => {
                item.classList.add('hidden');
            });
            
            // Show the selected visit
            const visitItem = document.querySelector(`[data-visit-index="${index}"]`);
            if (visitItem) {
                visitItem.classList.remove('hidden');
            }
            
            // Update counter
            document.getElementById('visitCounter').textContent = `${index + 1} of ${totalVisits}`;
            
            // Update navigation buttons
            document.getElementById('prevBtn').disabled = index === 0;
            document.getElementById('nextBtn').disabled = index === totalVisits - 1;
            
            // Update button styles
            if (index === 0) {
                document.getElementById('prevBtn').classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                document.getElementById('prevBtn').classList.remove('opacity-50', 'cursor-not-allowed');
            }
            
            if (index === totalVisits - 1) {
                document.getElementById('nextBtn').classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                document.getElementById('nextBtn').classList.remove('opacity-50', 'cursor-not-allowed');
            }
            
            // Update dots indicator
            document.querySelectorAll('.visit-dot').forEach((dot, dotIndex) => {
                if (dotIndex === index) {
                    dot.classList.remove('bg-gray-500');
                    dot.classList.add('bg-blue-400');
                } else {
                    dot.classList.remove('bg-blue-400');
                    dot.classList.add('bg-gray-500');
                }
            });
            
            // Update current index
            currentVisitIndex = index;
        }

        function nextVisit() {
            if (currentVisitIndex < totalVisits - 1) {
                currentVisitIndex++;
                showVisit(currentVisitIndex);
            }
        }

        function previousVisit() {
            if (currentVisitIndex > 0) {
                currentVisitIndex--;
                showVisit(currentVisitIndex);
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (document.getElementById('facilityVisitModal').classList.contains('hidden')) {
                return; // Only handle keys when modal is open
            }
            
            if (e.key === 'ArrowRight') {
                nextVisit();
            } else if (e.key === 'ArrowLeft') {
                previousVisit();
            }
        });

        // Update countdown every minute
        setInterval(updateCountdown, 60000);
        updateCountdown(); // Initial update
    </script>
</body>
</html> 