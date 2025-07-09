<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Admin Dashboard</title>
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
                    <p class="text-sm font-semibold">{{ Auth::user()->username }}</p>
                    <p class="text-xs text-gray-400">System Admin</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ substr(Auth::user()->username, 0, 2) }}</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="sidebar w-64 p-6" id="sidebar">
            <nav class="space-y-2">
                <a href="#" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
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
        <main class="flex-1 p-6 main-content">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-bold font-space gradient-text mb-2">Admin Dashboard</h1>
                    <p class="text-white text-lg">Overview of system statistics and recent activity</p>
                </div>
                <a href="{{ route('admin.dashboard.export.csv') }}" class="btn-primary px-6 py-2 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Export Dashboard Data
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card p-6 rounded-2xl text-center float-animation">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $totalUsers ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Total Users</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 85%"></div>
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.2s">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-check text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $activeUsers ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Active Users</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 92%"></div>
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.4s">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $pendingApprovals ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Pending Approvals</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 45%"></div>
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.6s">
                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heartbeat text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2 text-green-400">Excellent</h3>
                    <p class="text-gray-400 mb-2">System Health</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 95%"></div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- System Activity Chart -->
                <div class="lg:col-span-2">
                    <div class="glass-card p-6 rounded-2xl">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold font-space">System Performance</h2>
                            <select id="performanceRange" class="bg-gray-800 border border-gray-400 rounded-lg px-3 py-1 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="7">Last 7 days</option>
                                <option value="30">Last 30 days</option>
                                <option value="90">Last 90 days</option>
                            </select>
                        </div>
                        <div class="chart-container h-96 flex items-center justify-center">
                            <canvas id="performanceChart" width="900" height="400"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="glass-card p-6 rounded-2xl">
                    <h2 class="text-xl font-bold font-space mb-6">Quick Actions</h2>
                    <div class="space-y-4">
                        <a href="{{ route('admin.system-settings.index') }}" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center space-x-2 relative overflow-hidden">
                            <i class="fas fa-cogs"></i>
                            <span>System Settings</span>
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center space-x-2 relative overflow-hidden">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Reports</span>
                        </a>
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center space-x-2 relative overflow-hidden">
                            <i class="fas fa-shield-alt"></i>
                            <span>Audit Logs</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-8">
                <div class="glass-card p-6 rounded-2xl">
                    <h2 class="text-2xl font-bold font-space mb-6">Recent System Activity</h2>
                    <div class="space-y-4">
                        @forelse ($recentActivity as $activity)
                            <div class="activity-item p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-plus text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">{{ $activity->description }}</p>
                                            <p class="text-sm text-gray-400">{{ $activity->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400">No recent activity</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Vendor Management Section -->
            <div class="mt-8" id="vendor-management">
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold font-space">Vendor Applications</h2>
                        <div class="flex space-x-2">
                            <select class="bg-transparent border border-gray-600 rounded-lg px-3 py-1 text-sm" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="pending_visit">Pending Visit</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            <button class="btn-primary px-4 py-2 rounded-lg text-sm">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="py-3 px-4 font-semibold">Business Name</th>
                                    <th class="py-3 px-4 font-semibold">Type</th>
                                    <th class="py-3 px-4 font-semibold">Status</th>
                                    <th class="py-3 px-4 font-semibold">Score</th>
                                    <th class="py-3 px-4 font-semibold">Applied</th>
                                    <th class="py-3 px-4 font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vendorApplications ?? [] as $vendor)
                                    <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition-colors">
                                        <td class="py-4 px-4">
                                            <div>
                                                <p class="font-medium">{{ $vendor->application_data['business_name'] ?? 'N/A' }}</p>
                                                <p class="text-sm text-gray-400">{{ $vendor->user->email }}</p>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs">
                                                {{ ucfirst($vendor->application_data['business_type'] ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($vendor->status == 'pending')
                                                <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Pending</span>
                                            @elseif($vendor->status == 'pending_visit')
                                                <span class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs">Visit Scheduled</span>
                                            @elseif($vendor->status == 'approved')
                                                <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Approved</span>
                                            @elseif($vendor->status == 'rejected')
                                                <span class="px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($vendor->total_score !== null)
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium">{{ number_format($vendor->total_score, 1) }}%</span>
                                                    <div class="w-16 bg-gray-700 rounded-full h-2">
                                                        <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full" 
                                                             style="width: {{ $vendor->total_score }}%"></div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400">Pending</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="text-sm text-gray-400">{{ $vendor->created_at->format('M d, Y') }}</span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex space-x-2">
                                                <button class="btn-primary px-3 py-1 rounded text-xs" 
                                                        onclick="viewVendorDetails({{ $vendor->id }})">
                                                    <i class="fas fa-eye mr-1"></i>
                                                    View
                                                </button>
                                                @if($vendor->status == 'pending')
                                                    <button class="bg-green-500 hover:bg-green-600 px-3 py-1 rounded text-xs text-white transition-colors"
                                                            onclick="scheduleVisit({{ $vendor->id }})">
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        Schedule Visit
                                                    </button>
                                                @elseif($vendor->status == 'pending_visit')
                                                    <button class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-xs text-white transition-colors"
                                                            onclick="approveVendor({{ $vendor->id }})">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Approve
                                                    </button>
                                                    <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-xs text-white transition-colors"
                                                            onclick="rejectVendor({{ $vendor->id }})">
                                                        <i class="fas fa-times mr-1"></i>
                                                        Reject
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 px-4 text-center text-gray-400">
                                            <i class="fas fa-building text-3xl mb-3"></i>
                                            <p>No vendor applications found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Facility Visit Modal -->
    <div id="scheduleVisitModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white text-gray-900 rounded-xl shadow-lg w-full max-w-md p-6 relative">
            <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700" onclick="closeScheduleVisitModal()">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-xl font-bold mb-4">Schedule Facility Visit</h3>
            <form id="scheduleVisitForm">
                <input type="hidden" id="visitVendorId" name="vendor_id">
                <div class="mb-4">
                    <label for="scheduled_at" class="block font-medium mb-1">Visit Date</label>
                    <input type="date" id="scheduled_at" name="scheduled_at" class="form-input w-full rounded border-gray-300" required>
                </div>
                <div class="mb-4">
                    <label for="notes" class="block font-medium mb-1">Notes (optional)</label>
                    <textarea id="notes" name="notes" class="form-input w-full rounded border-gray-300" rows="3"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 rounded bg-gray-200 text-gray-700" onclick="closeScheduleVisitModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Schedule</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Approve Vendor Confirmation Modal -->
    <div id="approveVendorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white text-gray-900 rounded-xl shadow-lg w-full max-w-sm p-6 relative">
            <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700" onclick="closeApproveVendorModal()">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-xl font-bold mb-4">Approve Vendor</h3>
            <p>Are you sure you want to approve this vendor? This will grant them full supplier access.</p>
            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" class="px-4 py-2 rounded bg-gray-200 text-gray-700" onclick="closeApproveVendorModal()">Cancel</button>
                <button type="button" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700" onclick="confirmApproveVendor()">Approve</button>
            </div>
        </div>
    </div>

    <!-- Reject Vendor Modal -->
    <div id="rejectVendorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white text-gray-900 rounded-xl shadow-lg w-full max-w-sm p-6 relative">
            <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700" onclick="closeRejectVendorModal()">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-xl font-bold mb-4">Reject Vendor</h3>
            <form id="rejectVendorForm">
                <input type="hidden" id="rejectVendorId" name="vendor_id">
                <div class="mb-4">
                    <label for="reject_reason" class="block font-medium mb-1">Reason for rejection</label>
                    <textarea id="reject_reason" name="reason" class="form-input w-full rounded border-gray-300" rows="3" required></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 rounded bg-gray-200 text-gray-700" onclick="closeRejectVendorModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Reject</button>
                </div>
            </form>
        </div>
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

        const canvas = document.getElementById('performanceChart');
        const ctx = canvas.getContext('2d');

        function drawChart(labels, data) {
            const canvasWidth = canvas.width;
            const canvasHeight = canvas.height;
            const padding = 60;
            const chartWidth = canvasWidth - 2 * padding;
            const chartHeight = canvasHeight - 2 * padding;

            ctx.clearRect(0, 0, canvasWidth, canvasHeight);

            // Draw Y axis labels (0, 25%, 50%, 75%, 100% of max)
            const maxValue = Math.max(...data, 10);
            ctx.font = '16px Inter';
            ctx.fillStyle = '#aaa';
            ctx.textAlign = 'right';
            ctx.textBaseline = 'middle';
            for (let i = 0; i <= 4; i++) {
                const value = Math.round(maxValue * (1 - i / 4));
                const y = padding + (chartHeight * i) / 4;
                ctx.fillText(value, padding - 10, y);
                ctx.beginPath();
                ctx.moveTo(padding, y);
                ctx.lineTo(canvasWidth - padding, y);
                ctx.strokeStyle = '#222';
                ctx.lineWidth = 1;
                ctx.setLineDash([4, 4]);
                ctx.stroke();
                ctx.setLineDash([]);
            }
            ctx.textAlign = 'left';
            ctx.textBaseline = 'alphabetic';

            // Draw X axis labels (reduce density for 30/90 days)
            ctx.font = '15px Inter';
            ctx.fillStyle = '#aaa';
            let labelStep = 1;
            if (labels.length > 20 && labels.length <= 40) labelStep = 5;
            if (labels.length > 40) labelStep = 10;
            labels.forEach((label, index) => {
                const x = padding + (index * chartWidth) / (labels.length - 1);
                // Always show first, last, and step labels
                if (index === 0 || index === labels.length - 1 || index % labelStep === 0) {
                    ctx.fillText(label, x - 25, canvasHeight - padding + 30);
                }
            });

            // Area gradient
            const gradient = ctx.createLinearGradient(0, 0, 0, canvasHeight);
            gradient.addColorStop(0, 'rgba(79, 172, 254, 0.8)');
            gradient.addColorStop(1, 'rgba(79, 172, 254, 0.1)');
            ctx.fillStyle = gradient;
            ctx.beginPath();
            ctx.moveTo(padding, canvasHeight - padding);
            data.forEach((value, index) => {
                const x = padding + (index * chartWidth) / (data.length - 1);
                const y = padding + chartHeight - (value / maxValue) * chartHeight;
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

            // Line
            ctx.strokeStyle = '#4FACFE';
            ctx.lineWidth = 4;
            ctx.beginPath();
            data.forEach((value, index) => {
                const x = padding + (index * chartWidth) / (data.length - 1);
                const y = padding + chartHeight - (value / maxValue) * chartHeight;
                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            });
            ctx.stroke();

            // Dots and data values
            ctx.font = 'bold 16px Inter';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            data.forEach((value, index) => {
                const x = padding + (index * chartWidth) / (data.length - 1);
                const y = padding + chartHeight - (value / maxValue) * chartHeight;
                ctx.beginPath();
                ctx.arc(x, y, 7, 0, 2 * Math.PI);
                ctx.fillStyle = '#4FACFE';
                ctx.fill();
                ctx.strokeStyle = 'white';
                ctx.lineWidth = 3;
                ctx.stroke();
                // Draw value above dot
                ctx.fillStyle = '#fff';
                ctx.fillText(value, x, y - 12);
            });
            ctx.textAlign = 'left';
            ctx.textBaseline = 'alphabetic';
        }

        function loadPerformanceData(range = 30) {
            fetch(`/admin/dashboard/performance-data?range=${range}`)
                .then(response => response.json())
                .then(result => {
                    drawChart(result.labels, result.data);
                });
        }

        const rangeSelect = document.getElementById('performanceRange');
        rangeSelect.addEventListener('change', function() {
            loadPerformanceData(this.value);
        });

        window.addEventListener('resize', () => {
            loadPerformanceData(rangeSelect.value);
        });

        setTimeout(() => loadPerformanceData(rangeSelect.value), 1000);

        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });

        setInterval(() => {
            const randomCard = document.querySelectorAll('.stat-card h3')[Math.floor(Math.random() * 3)];
            const currentValue = parseInt(randomCard.textContent.replace(/,/g, ''));
            const newValue = currentValue + Math.floor(Math.random() * 10) - 5;
            randomCard.textContent = newValue.toLocaleString();
        }, 10000);

        // Vendor Management Functions
        function viewVendorDetails(vendorId) {
            // TODO: Implement modal or redirect to vendor details page
            alert('View vendor details for ID: ' + vendorId);
        }

        // Modal logic
        let currentVendorId = null;
        function scheduleVisit(vendorId) {
            currentVendorId = vendorId;
            document.getElementById('visitVendorId').value = vendorId;
            document.getElementById('scheduleVisitModal').classList.remove('hidden');
        }
        function closeScheduleVisitModal() {
            document.getElementById('scheduleVisitModal').classList.add('hidden');
            document.getElementById('scheduleVisitForm').reset();
        }

        // AJAX form submission
        const scheduleVisitForm = document.getElementById('scheduleVisitForm');
        scheduleVisitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const vendorId = document.getElementById('visitVendorId').value;
            const scheduledAt = document.getElementById('scheduled_at').value;
            const notes = document.getElementById('notes').value;
            
            fetch(`/admin/vendors/${vendorId}/schedule-visit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ scheduled_at: scheduledAt, notes: notes })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeScheduleVisitModal();
                    alert('Facility visit scheduled successfully!');
                    window.location.reload(); // Optionally refresh the table
                } else {
                    alert('Error: ' + (data.message || 'Could not schedule visit.'));
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        });

        // Approve Vendor Modal logic
        let approveVendorId = null;
        function approveVendor(vendorId) {
            approveVendorId = vendorId;
            document.getElementById('approveVendorModal').classList.remove('hidden');
        }
        function closeApproveVendorModal() {
            document.getElementById('approveVendorModal').classList.add('hidden');
            approveVendorId = null;
        }
        function confirmApproveVendor() {
            if (!approveVendorId) return;
            fetch(`/admin/vendors/${approveVendorId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeApproveVendorModal();
                    alert('Vendor approved successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Could not approve vendor.'));
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        // Reject Vendor Modal logic
        let rejectVendorId = null;
        function rejectVendor(vendorId) {
            rejectVendorId = vendorId;
            document.getElementById('rejectVendorId').value = vendorId;
            document.getElementById('rejectVendorModal').classList.remove('hidden');
        }
        function closeRejectVendorModal() {
            document.getElementById('rejectVendorModal').classList.add('hidden');
            document.getElementById('rejectVendorForm').reset();
            rejectVendorId = null;
        }
        document.getElementById('rejectVendorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const reason = document.getElementById('reject_reason').value;
            if (!rejectVendorId || !reason) return;
            fetch(`/admin/vendors/${rejectVendorId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeRejectVendorModal();
                    alert('Vendor rejected successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Could not reject vendor.'));
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        });

        // Status filter functionality
        document.getElementById('statusFilter').addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const statusCell = row.querySelector('td:nth-child(3) span');
                if (status === '' || (statusCell && statusCell.textContent.toLowerCase().includes(status))) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>