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

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            margin-top: 8px;
        }

        .progress-fill {
            height: 100%;
            background: var(--accent-gradient);
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        .logo-pulse {
            animation: pulse 2s infinite;
        }

        .notification-dot {
            background: var(--accent-gradient);
            animation: pulse 2s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
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
                <p class="text-xs text-white">Wheat Supply Chain Management</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-6">
            <div class="relative">
                <i class="fas fa-bell text-white text-xl cursor-pointer hover:text-white transition-colors"></i>
                <span class="notification-dot absolute -top-1 -right-1 w-3 h-3 rounded-full"></span>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm font-semibold text-blue-300">{{ Auth::user()->username }}</p>
                    <p class="text-xs text-white">System Admin</p>
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
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="{{ route('admin.vendors') }}" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-building w-5"></i>
                    <span class="font-medium">Vendor Management</span>
                </a>
                <a href="{{ route('admin.inventory.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-seedling w-5"></i>
                    <span class="font-medium">Inventory</span>
                </a>
                <a href="{{ route('admin.supply-chain.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-truck w-5"></i>
                    <span class="font-medium">Supply Chain</span>
                </a>
                <a href="{{ route('admin.analytics') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-chart-line w-5"></i>
                    <span class="font-medium">Analytics</span>
                </a>
                <a href="{{ route('admin.system-settings.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-cog w-5"></i>
                    <span class="font-medium">System Settings</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-file-alt w-5"></i>
                    <span class="font-medium">Reports</span>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-shield-alt w-5"></i>
                    <span class="font-medium">Audit Logs</span>
                </a>
                <a href="{{ route('chat.index') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-white relative">
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
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold font-space gradient-text mb-2">Vendor Management</h1>
                <p class="text-white text-lg">Manage supplier applications and vendor relationships</p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card p-6 rounded-2xl text-center float-animation">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2 text-blue-300">{{ $vendors->total() }}</h3>
                    <p class="text-white mb-2">Total Vendors</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 85%"></div>
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.2s">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2 text-yellow-300">{{ $vendors->where('status', 'pending')->count() }}</h3>
                    <p class="text-white mb-2">Pending Review</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 45%"></div>
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.4s">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2 text-green-300">{{ $vendors->where('status', 'approved')->count() }}</h3>
                    <p class="text-white mb-2">Approved</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 92%"></div>
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.6s">
                    <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-times-circle text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2 text-red-300">{{ $vendors->where('status', 'rejected')->count() }}</h3>
                    <p class="text-white mb-2">Rejected</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 25%"></div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="glass-card p-6 rounded-2xl mb-6">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-col md:flex-row gap-4 flex-1">
                        <div class="relative">
                            <input type="text" id="search" placeholder="Search vendors..." 
                                   class="w-full md:w-80 px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="absolute right-3 top-2.5 w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <select id="status-filter" class="px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <a href="{{ route('admin.vendors.export.csv') }}" class="btn-primary px-6 py-2 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Export Data
                    </a>
                </div>
            </div>

            <!-- Vendors Table -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Vendor</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Score</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Applied</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($vendors as $vendor)
                            <tr class="hover:bg-gray-800/30 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            {{ strtoupper(substr($vendor->application_data['business_name'] ?? $vendor->user->name ?? 'N/A', 0, 2)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white">
                                                {{ $vendor->application_data['business_name'] ?? $vendor->user->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-white">
                                                {{ $vendor->application_data['business_type'] ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-white">{{ $vendor->user->email ?? 'N/A' }}</div>
                                    <div class="text-sm text-white">{{ $vendor->application_data['phone'] ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($vendor->status === 'approved')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-300 border border-green-500/30">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Approved
                                        </span>
                                    @elseif($vendor->status === 'rejected')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-300 border border-red-500/30">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            Rejected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($vendor->total_score)
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-700 rounded-full h-2 mr-2">
                                                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full" style="width: {{ $vendor->total_score }}%"></div>
                                            </div>
                                            <span class="text-sm text-white">{{ number_format($vendor->total_score, 1) }}%</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-white">Not scored</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-white">
                                    {{ $vendor->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="viewVendor({{ $vendor->id }})" class="btn-secondary px-3 py-1.5 rounded-lg text-xs flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </button>
                                        
                                        @if($vendor->status === 'pending')
                                        <button onclick="approveVendor({{ $vendor->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs flex items-center gap-1 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Approve
                                        </button>
                                        <button onclick="rejectVendor({{ $vendor->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs flex items-center gap-1 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Reject
                                        </button>
                                        @endif
                                        
                                        <button onclick="scheduleVisit({{ $vendor->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs flex items-center gap-1 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Visit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($vendors->hasPages())
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-white">
                    Showing {{ $vendors->firstItem() }} to {{ $vendors->lastItem() }} of {{ $vendors->total() }} results
                </div>
                <div class="flex space-x-2">
                    {{ $vendors->links() }}
                </div>
            </div>
            @endif
        </main>
    </div>

    <!-- Vendor Details Modal -->
    <div id="vendorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="glass-card rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-white">Vendor Details</h3>
                        <button onclick="closeVendorModal()" class="text-white hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="vendorModalContent" class="p-6">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <script>
    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });

    function viewVendor(vendorId) {
        // Show loading state
        document.getElementById('vendorModal').classList.remove('hidden');
        document.getElementById('vendorModalContent').innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div></div>';
        
        // Fetch vendor details
        fetch(`/admin/vendors/${vendorId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayVendorDetails(data.vendor);
                } else {
                    document.getElementById('vendorModalContent').innerHTML = '<div class="text-red-400 text-center py-8">Error loading vendor details</div>';
                }
            })
            .catch(error => {
                document.getElementById('vendorModalContent').innerHTML = '<div class="text-red-400 text-center py-8">Error loading vendor details</div>';
            });
    }

    function displayVendorDetails(vendor) {
        const content = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Business Information</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-white">Business Name</label>
                            <p class="text-white">${vendor.application_data?.business_name || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="text-sm text-white">Business Type</label>
                            <p class="text-white">${vendor.application_data?.business_type || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="text-sm text-white">Registration Number</label>
                            <p class="text-white">${vendor.application_data?.registration_number || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="text-sm text-white">Years in Business</label>
                            <p class="text-white">${vendor.application_data?.years_in_business || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Contact Information</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-white">Email</label>
                            <p class="text-white">${vendor.user?.email || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="text-sm text-white">Phone</label>
                            <p class="text-white">${vendor.application_data?.phone || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="text-sm text-white">Address</label>
                            <p class="text-white">${vendor.application_data?.address || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <h4 class="text-lg font-semibold text-white mb-4">Scores</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="glass-card p-4 rounded-xl border border-gray-700/50">
                        <div class="text-center">
                            <p class="text-sm text-white">Financial Score</p>
                            <p class="text-2xl font-bold text-white">${vendor.score_financial || 0}%</p>
                        </div>
                    </div>
                    <div class="glass-card p-4 rounded-xl border border-gray-700/50">
                        <div class="text-center">
                            <p class="text-sm text-white">Reputation Score</p>
                            <p class="text-2xl font-bold text-white">${vendor.score_reputation || 0}%</p>
                        </div>
                    </div>
                    <div class="glass-card p-4 rounded-xl border border-gray-700/50">
                        <div class="text-center">
                            <p class="text-sm text-white">Compliance Score</p>
                            <p class="text-2xl font-bold text-white">${vendor.score_compliance || 0}%</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('vendorModalContent').innerHTML = content;
    }

    function closeVendorModal() {
        document.getElementById('vendorModal').classList.add('hidden');
    }

    function approveVendor(vendorId) {
        if (confirm('Are you sure you want to approve this vendor?')) {
            fetch(`/admin/vendors/${vendorId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error approving vendor');
                }
            });
        }
    }

    function rejectVendor(vendorId) {
        const reason = prompt('Please provide a reason for rejection:');
        if (reason) {
            fetch(`/admin/vendors/${vendorId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error rejecting vendor');
                }
            });
        }
    }

    function scheduleVisit(vendorId) {
        // Show visit scheduling modal
        showVisitModal(vendorId);
    }

    function showVisitModal(vendorId) {
        const modal = document.createElement('div');
        modal.id = 'visitModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="glass-card rounded-2xl max-w-md w-full mx-4">
                <div class="p-6 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-white">Schedule Facility Visit</h3>
                        <button onclick="closeVisitModal()" class="text-white hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <form id="visitForm">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-white mb-2">Visit Date & Time</label>
                            <input type="datetime-local" id="scheduled_at" name="scheduled_at" required
                                   class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   min="${new Date().toISOString().slice(0, 16)}">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-white mb-2">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Add any notes about the visit..."></textarea>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeVisitModal()" 
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                Schedule Visit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Handle form submission
        document.getElementById('visitForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitVisitSchedule(vendorId);
        });
    }

    function closeVisitModal() {
        const modal = document.getElementById('visitModal');
        if (modal) {
            modal.remove();
        }
    }

    function submitVisitSchedule(vendorId) {
        const scheduledAt = document.getElementById('scheduled_at').value;
        const notes = document.getElementById('notes').value;
        
        if (!scheduledAt) {
            alert('Please select a date and time for the visit.');
            return;
        }
        
        // Show loading state
        const submitBtn = document.querySelector('#visitForm button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Scheduling...';
        submitBtn.disabled = true;
        
        fetch(`/admin/vendors/${vendorId}/schedule-visit`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                scheduled_at: scheduledAt,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeVisitModal();
                alert('Facility visit scheduled successfully!');
                location.reload(); // Refresh to show updated status
            } else {
                alert('Error scheduling visit: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error scheduling visit: ' + error.message);
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }

    // Search and filter functionality
    document.getElementById('search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    document.getElementById('status-filter').addEventListener('change', function(e) {
        const status = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            row.style.display = !status || statusCell.includes(status) ? '' : 'none';
        });
    });
    </script>
</body>
</html> 