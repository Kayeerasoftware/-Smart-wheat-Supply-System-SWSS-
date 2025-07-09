<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS System Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .main-content {
            margin-left: 16rem; /* 256px for sidebar width */
            margin-top: 5rem; /* 80px for navigation height */
            min-height: calc(100vh - 5rem);
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
            .main-content {
                margin-left: 0;
                margin-top: 5rem;
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
    <nav class="fixed-nav px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <button class="md:hidden text-white" id="sidebarToggle">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center logo-pulse">
                <span class="text-white font-bold">A</span>
            </div>
            <div>
                <h1 class="text-xl font-bold font-space gradient-text">SWSS Admin</h1>
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
                    <p class="text-sm font-semibold">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">System Admin</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 2) }}</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="sidebar w-64 p-6" id="sidebar">
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
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
                <a href="{{ route('admin.supply-chain.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-truck w-5"></i>
                    <span class="font-medium">Supply Chain</span>
                </a>
                <a href="{{ route('admin.analytics') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-chart-line w-5"></i>
                    <span class="font-medium">Analytics</span>
                </a>
                <a href="{{ route('admin.system-settings.index') }}" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
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
        <main class="flex-1 p-6 main-content">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2 font-space">System Settings</h1>
                <p class="text-gray-400">Manage system configuration, users, and security settings</p>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card p-6 rounded-2xl text-center float-animation">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $systemStats['total_users'] }}</h3>
                    <p class="text-gray-400 mb-2">Total Users</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 85%"></div>
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.2s">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-check text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2 text-green-400">{{ $systemStats['active_users'] }}</h3>
                    <p class="text-gray-400 mb-2">Active Users</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 92%"></div>
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.4s">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2 text-yellow-400">{{ $systemStats['pending_users'] }}</h3>
                    <p class="text-gray-400 mb-2">Pending Users</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 45%"></div>
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.6s">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2 text-purple-400">{{ $systemStats['admin_users'] }}</h3>
                    <p class="text-gray-400 mb-2">Admin Users</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 95%"></div>
                    </div>
                </div>
            </div>

            <!-- Settings Navigation -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- User Management -->
                <a href="{{ route('admin.system-settings.users') }}" class="group">
                    <div class="glass-card p-6 rounded-2xl hover:bg-white/10 transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-blue-100/20 rounded-full group-hover:bg-blue-200/30 transition-colors">
                                <i class="fas fa-users text-blue-400 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-white ml-4 font-space">User Management</h3>
                        </div>
                        <p class="text-gray-300">Manage user accounts, roles, and permissions</p>
                    </div>
                </a>

                <!-- System Configuration -->
                <a href="{{ route('admin.system-settings.configuration') }}" class="group">
                    <div class="glass-card p-6 rounded-2xl hover:bg-white/10 transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-green-100/20 rounded-full group-hover:bg-green-200/30 transition-colors">
                                <i class="fas fa-cogs text-green-400 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-white ml-4 font-space">Configuration</h3>
                        </div>
                        <p class="text-gray-300">System settings and application configuration</p>
                    </div>
                </a>

                <!-- Security Settings -->
                <a href="{{ route('admin.system-settings.security') }}" class="group">
                    <div class="glass-card p-6 rounded-2xl hover:bg-white/10 transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-red-100/20 rounded-full group-hover:bg-red-200/30 transition-colors">
                                <i class="fas fa-shield-alt text-red-400 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-white ml-4 font-space">Security</h3>
                        </div>
                        <p class="text-gray-300">Security settings and threat monitoring</p>
                    </div>
                </a>

                <!-- Audit Logs -->
                <a href="{{ route('admin.system-settings.audit-logs') }}" class="group">
                    <div class="glass-card p-6 rounded-2xl hover:bg-white/10 transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-purple-100/20 rounded-full group-hover:bg-purple-200/30 transition-colors">
                                <i class="fas fa-clipboard-list text-purple-400 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-white ml-4 font-space">Audit Logs</h3>
                        </div>
                        <p class="text-gray-300">System activity logs and audit trail</p>
                    </div>
                </a>

                <!-- System Health -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-indigo-100/20 rounded-full">
                            <i class="fas fa-heartbeat text-indigo-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-white ml-4 font-space">System Health</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-300">Database</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $systemHealth['database_connections']['status'] === 'healthy' ? 'bg-green-100/20 text-green-400' : 'bg-red-100/20 text-red-400' }}">
                                {{ ucfirst($systemHealth['database_connections']['status']) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-300">Storage</span>
                            <span class="text-sm font-medium text-white">{{ $systemHealth['storage_usage']['percentage'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-300">Memory</span>
                            <span class="text-sm font-medium text-white">{{ $systemHealth['memory_usage']['current'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="glass-card p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white font-space">Recent System Activities</h2>
                    <a href="{{ route('admin.system-settings.audit-logs') }}" class="text-blue-400 hover:text-blue-300 font-medium">View All</a>
                </div>
                
                <div class="space-y-4">
                    @forelse($recentActivities as $activity)
                    <div class="activity-item p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-info text-white text-xs"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-white">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100/20 text-gray-300">
                                    {{ ucfirst($activity->type) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard-list text-4xl text-gray-500 mb-4"></i>
                        <p class="text-gray-400">No recent activities</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>

    <script>
        // Mobile sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });
    </script>
</body>
</html> 