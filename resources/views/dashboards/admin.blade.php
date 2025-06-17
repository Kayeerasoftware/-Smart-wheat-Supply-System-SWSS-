<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Admin Dashboard</title>
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
    <nav class="glass-card mx-4 mt-4 mb-6 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <button class="md:hidden text-white" id="sidebarToggle">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center logo-pulse">
                <span class="text-white font-bold">S</span>
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
        <aside class="sidebar w-64 min-h-screen p-6" id="sidebar">
            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-users w-5"></i>
                    <span class="font-medium">User Management</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-wheat-awn w-5"></i>
                    <span class="font-medium">Inventory</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-truck w-5"></i>
                    <span class="font-medium">Supply Chain</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-chart-line w-5"></i>
                    <span class="font-medium">Analytics</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-cog w-5"></i>
                    <span class="font-medium">System Settings</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-file-alt w-5"></i>
                    <span class="font-medium">Reports</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-shield-alt w-5"></i>
                    <span class="font-medium">Audit Logs</span>
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
        <main class="flex-1 p-6">
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
                            <select class="bg-transparent border border-gray-600 rounded-lg px-3 py-1 text-sm">
                                <option>Last 7 days</option>
                                <option>Last 30 days</option>
                                <option>Last 90 days</option>
                            </select>
                        </div>
                        <div class="chart-container h-64 flex items-center justify-center">
                            <canvas id="performanceChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="glass-card p-6 rounded-2xl">
                    <h2 class="text-xl font-bold font-space mb-6">Quick Actions</h2>
                    <div class="space-y-4">
                        <a href="#" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center space-x-2 relative overflow-hidden">
                            <i class="fas fa-user-plus"></i>
                            <span>Add New User</span>
                        </a>
                        <a href="#" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center space-x-2 relative overflow-hidden">
                            <i class="fas fa-cogs"></i>
                            <span>System Settings</span>
                        </a>
                        <a href="#" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center space-x-2 relative overflow-hidden">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Reports</span>
                        </a>
                        <a href="#" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center space-x-2 relative overflow-hidden">
                            <i class="fas fa-history"></i>
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
        </main>
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
        
        const data = [65, 59, 80, 81, 56, 55, 40, 85, 75, 90, 88, 92];
        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        function drawChart() {
            const canvasWidth = canvas.width;
            const canvasHeight = canvas.height;
            const padding = 40;
            const chartWidth = canvasWidth - 2 * padding;
            const chartHeight = canvasHeight - 2 * padding;
            
            ctx.clearRect(0, 0, canvasWidth, canvasHeight);
            
            const gradient = ctx.createLinearGradient(0, 0, 0, canvasHeight);
            gradient.addColorStop(0, 'rgba(79, 172, 254, 0.8)');
            gradient.addColorStop(1, 'rgba(79, 172, 254, 0.1)');
            
            ctx.fillStyle = gradient;
            ctx.beginPath();
            ctx.moveTo(padding, canvasHeight - padding);
            
            data.forEach((value, index) => {
                const x = padding + (index * chartWidth) / (data.length - 1);
                const y = canvasHeight - padding - (value / 100) * chartHeight;
                
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
            
            ctx.strokeStyle = '#4FACFE';
            ctx.lineWidth = 3;
            ctx.beginPath();
            
            data.forEach((value, index) => {
                const x = padding + (index * chartWidth) / (data.length - 1);
                const y = canvasHeight - padding - (value / 100) * chartHeight;
                
                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            });
            
            ctx.stroke();
            
            data.forEach((value, index) => {
                const x = padding + (index * chartWidth) / (data.length - 1);
                const y = canvasHeight - padding - (value / 100) * chartHeight;
                
                ctx.beginPath();
                ctx.arc(x, y, 4, 0, 2 * Math.PI);
                ctx.fillStyle = '#4FACFE';
                ctx.fill();
                ctx.strokeStyle = 'white';
                ctx.lineWidth = 2;
                ctx.stroke();
            });
        }
        
        setTimeout(drawChart, 1000);
        
        window.addEventListener('resize', () => {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            drawChart();
        });

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
    </script>
</body>
</html>