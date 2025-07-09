<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Admin Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            margin-left: 16rem;
            margin-top: 5rem;
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

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
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
    </style>
</head>
<body class="text-white overflow-x-hidden">
    <!-- Top Navigation -->
    <nav class="fixed-nav px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <button class="md:hidden text-white" id="sidebarToggle">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                <span class="text-white font-bold">A</span>
            </div>
            <div>
                <h1 class="text-xl font-bold font-space gradient-text">SWSS Admin</h1>
                <p class="text-xs text-gray-400">Analytics Dashboard</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-6">
            <div class="relative">
                <i class="fas fa-bell text-gray-300 text-xl cursor-pointer hover:text-white transition-colors"></i>
                <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-gradient-to-r from-pink-500 to-rose-500"></span>
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
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold font-space gradient-text mb-2">Analytics Dashboard</h1>
                <p class="text-gray-400">Comprehensive insights into your supply chain operations</p>
            </div>

            <!-- Key Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card p-6 rounded-2xl text-center float-animation">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $totalUsers ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Total Users</p>
                    <div class="text-sm {{ $userGrowthPercent > 0 ? 'text-green-400' : ($userGrowthPercent < 0 ? 'text-red-400' : 'text-gray-400') }}">
                        @if($userGrowthPercent > 0)
                            <i class="fas fa-arrow-up"></i> +{{ $userGrowthPercent }}% this month
                        @elseif($userGrowthPercent < 0)
                            <i class="fas fa-arrow-down"></i> {{ $userGrowthPercent }}% this month
                        @else
                            <i class="fas fa-minus"></i> 0% this month
                        @endif
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.2s">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-building text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $totalVendors ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Total Vendors</p>
                    <div class="text-sm text-yellow-400">
                        <i class="fas fa-clock"></i> {{ $pendingVendors ?? 0 }} pending
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.4s">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-box text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $totalProducts ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Total Products</p>
                    <div class="text-sm text-red-400">
                        <i class="fas fa-exclamation-triangle"></i> {{ $lowStockProducts ?? 0 }} low stock
                    </div>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.6s">
                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-cart text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $totalOrders ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Total Orders</p>
                    <div class="text-sm text-blue-400">
                        <i class="fas fa-check-circle"></i> {{ $completedOrders ?? 0 }} completed
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- User Growth Chart -->
                <div class="glass-card p-6">
                    <h3 class="text-xl font-bold mb-4 gradient-text">User Growth Trend</h3>
                    <div class="chart-container p-4 h-80">
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                </div>

                <!-- Vendor Status Distribution -->
                <div class="glass-card p-6">
                    <h3 class="text-xl font-bold mb-4 gradient-text">Vendor Status Distribution</h3>
                    <div class="chart-container p-4 h-80">
                        <canvas id="vendorStatusChart"></canvas>
                    </div>
                </div>

                <!-- Order Trends -->
                <div class="glass-card p-6">
                    <h3 class="text-xl font-bold mb-4 gradient-text">Order Trends</h3>
                    <div class="chart-container p-4 h-80">
                        <canvas id="orderTrendsChart"></canvas>
                    </div>
                </div>

                <!-- User Role Distribution -->
                <div class="glass-card p-6">
                    <h3 class="text-xl font-bold mb-4 gradient-text">User Role Distribution</h3>
                    <div class="chart-container p-4 h-80">
                        <canvas id="userRoleChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Performers & Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Top Performing Vendors -->
                <div class="glass-card p-6">
                    <h3 class="text-xl font-bold mb-4 gradient-text">Top Performing Vendors</h3>
                    <div class="space-y-4">
                        @forelse($topVendors ?? [] as $index => $vendor)
                        <div class="flex items-center justify-between p-4 bg-white bg-opacity-5 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $vendor->user->username ?? 'Unknown' }}</p>
                                    <p class="text-sm text-gray-400">Score: {{ number_format($vendor->total_score ?? 0, 1) }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-green-400">{{ $vendor->status ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $vendor->created_at ? $vendor->created_at->diffForHumans() : 'N/A' }}</div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-400 text-center py-4">No vendor data available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="glass-card p-6">
                    <h3 class="text-xl font-bold mb-4 gradient-text">Recent Activities</h3>
                    <div class="space-y-4">
                        @forelse($recentActivities ?? [] as $activity)
                        <div class="flex items-start space-x-3 p-4 bg-white bg-opacity-5 rounded-lg">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-activity text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm">{{ $activity->description ?? 'System activity' }}</p>
                                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-400 text-center py-4">No recent activities</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // User Growth Chart
        const userCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userCtx, {
            type: 'line',
            data: {
                labels: @json($monthlyUsers->pluck('month') ?? []),
                datasets: [{
                    label: 'New Users',
                    data: @json($monthlyUsers->pluck('count') ?? []),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
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
                            color: '#ffffff'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    y: {
                        ticks: {
                            color: '#ffffff'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // Vendor Status Chart
        const vendorCtx = document.getElementById('vendorStatusChart').getContext('2d');
        new Chart(vendorCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Approved', 'Rejected'],
                datasets: [{
                    data: [{{ $pendingVendors ?? 0 }}, {{ $approvedVendors ?? 0 }}, {{ $rejectedVendors ?? 0 }}],
                    backgroundColor: [
                        '#f59e0b',
                        '#10b981',
                        '#ef4444'
                    ],
                    borderWidth: 0
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

        // Order Trends Chart
        const orderCtx = document.getElementById('orderTrendsChart').getContext('2d');
        new Chart(orderCtx, {
            type: 'bar',
            data: {
                labels: @json($monthlyOrders->pluck('month') ?? []),
                datasets: [{
                    label: 'Orders',
                    data: @json($monthlyOrders->pluck('count') ?? []),
                    backgroundColor: 'rgba(139, 92, 246, 0.8)',
                    borderColor: '#8b5cf6',
                    borderWidth: 2,
                    borderRadius: 8
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
                            color: '#ffffff'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    y: {
                        ticks: {
                            color: '#ffffff'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // User Role Chart
        const roleCtx = document.getElementById('userRoleChart').getContext('2d');
        new Chart(roleCtx, {
            type: 'pie',
            data: {
                labels: @json($usersByRole->keys() ?? []),
                datasets: [{
                    data: @json($usersByRole->values() ?? []),
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#06b6d4'
                    ],
                    borderWidth: 0
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

        // Mobile sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });
    </script>
</body>
</html> 