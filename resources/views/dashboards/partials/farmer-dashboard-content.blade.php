<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .glass-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .weather-card {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .weather-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        .demand-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .demand-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 8s ease-in-out infinite reverse;
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .stats-card:hover::before {
            left: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .content-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .btn-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-modern:hover::before {
            left: 100%;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .btn-secondary-modern {
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(102, 126, 234, 0.3);
            font-weight: 500;
        }
        
        .btn-secondary-modern:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .activity-item {
            transition: all 0.3s ease;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        
        .activity-item:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: translateX(4px);
        }
        
        .metric-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .metric-card:hover::before {
            left: 100%;
        }
        
        .season-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 14px;
            backdrop-filter: blur(10px);
        }
        
        .floating-elements {
            position: fixed;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .floating-icon {
            position: absolute;
            opacity: 0.1;
            animation: floatAround 20s infinite linear;
        }
        
        @keyframes floatAround {
            0% { transform: translateY(100vh) rotate(0deg); }
            100% { transform: translateY(-100px) rotate(360deg); }
        }
    </style>
</head>
<body class="p-6">
    <!-- Floating Background Elements -->
    <div class="floating-elements">
        <i class="fas fa-seedling floating-icon text-6xl" style="left: 10%; animation-delay: 0s;"></i>
        <i class="fas fa-sun floating-icon text-5xl" style="left: 80%; animation-delay: 5s;"></i>
        <i class="fas fa-wheat-awn floating-icon text-7xl" style="left: 60%; animation-delay: 10s;"></i>
        <i class="fas fa-tractor floating-icon text-6xl" style="left: 30%; animation-delay: 15s;"></i>
    </div>

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">🌾 Farmer Dashboard</h1>
            <p class="text-white/80">Monitor your crops, weather, and market trends</p>
        </div>

        <!-- Dashboard Content -->
        <div class="space-y-8">
            <!-- Weather and Demand Forecasts Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="weather-card p-6 rounded-2xl flex flex-col items-start justify-between relative">
                    <div class="flex items-center mb-4 relative z-10">
                        <div class="weather-icon text-4xl mr-4 pulse-dot">
                            <i class="fas fa-sun"></i>
                        </div>
                        <div>
                            <h5 class="font-bold mb-1 text-lg">Weather Forecast</h5>
                            <h3 class="text-3xl font-bold mb-2">24°C - Sunny</h3>
                            <p class="mb-0 text-white/90">Perfect conditions for wheat growth</p>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-white/80 relative z-10">
                        <div class="flex items-center gap-4">
                            <span><i class="fas fa-tint mr-1"></i>Humidity: 65%</span>
                            <span><i class="fas fa-wind mr-1"></i>Wind: 12 km/h</span>
                            <span><i class="fas fa-cloud-rain mr-1"></i>Rain: 0%</span>
                        </div>
                    </div>
                </div>

                <div class="demand-card p-6 rounded-2xl relative">
                    <div class="relative z-10">
                        <h5 class="font-bold mb-2 text-lg">Demand Forecast</h5>
                        <h3 class="text-2xl font-bold mb-3 text-green-200">Stable Demand</h3>
                        <p class="mb-3">Next month: 1,250 tons</p>
                        <div class="flex justify-between text-sm bg-white/10 p-3 rounded-lg">
                            <span>Current Price: $280.00/ton</span>
                            <span class="text-green-300 font-semibold">+5.2%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="stats-card p-6 rounded-2xl text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-seedling text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-2 text-gray-800">3</h3>
                    <p class="text-gray-600 mb-2 font-medium">Active Crops</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: 75%"></div>
                    </div>
                </div>

                <div class="stats-card p-6 rounded-2xl text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-scissors text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-2 text-gray-800">1</h3>
                    <p class="text-gray-600 mb-2 font-medium">Harvest Ready</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 33%"></div>
                    </div>
                </div>

                <div class="stats-card p-6 rounded-2xl text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-cart-arrow-down text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-2 text-gray-800">2</h3>
                    <p class="text-gray-600 mb-2 font-medium">Pending Orders</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: 50%"></div>
                    </div>
                </div>

                <div class="stats-card p-6 rounded-2xl text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dollar-sign text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-2 text-gray-800">$280</h3>
                    <p class="text-gray-600 mb-2 font-medium">Market Price</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: 70%"></div>
                    </div>
                </div>
            </div>

            <!-- Quality Metrics and Seasonal Planning -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="content-card p-6 rounded-2xl">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-award text-xl text-white"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800">Wheat Quality Metrics</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="metric-card text-center p-4 rounded-xl bg-green-50 border border-green-200">
                            <h6 class="font-bold mb-2 text-green-600">Protein Content</h6>
                            <h5 class="text-2xl font-bold text-gray-800 mb-1">12.5%</h5>
                            <small class="text-gray-500">Optimal: 11-13%</small>
                            <div class="w-full bg-green-200 rounded-full h-2 mt-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 83%"></div>
                            </div>
                        </div>
                        <div class="metric-card text-center p-4 rounded-xl bg-blue-50 border border-blue-200">
                            <h6 class="font-bold mb-2 text-blue-600">Moisture Content</h6>
                            <h5 class="text-2xl font-bold text-gray-800 mb-1">14%</h5>
                            <small class="text-gray-500">Optimal: 12-14%</small>
                            <div class="w-full bg-blue-200 rounded-full h-2 mt-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                        <div class="metric-card text-center p-4 rounded-xl bg-yellow-50 border border-yellow-200">
                            <h6 class="font-bold mb-2 text-yellow-600">Test Weight</h6>
                            <h5 class="text-2xl font-bold text-gray-800 mb-1">58 lb/bu</h5>
                            <small class="text-gray-500">Optimal: 60+ lb/bu</small>
                            <div class="w-full bg-yellow-200 rounded-full h-2 mt-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: 97%"></div>
                            </div>
                        </div>
                        <div class="metric-card text-center p-4 rounded-xl bg-purple-50 border border-purple-200">
                            <h6 class="font-bold mb-2 text-purple-600">Falling Number</h6>
                            <h5 class="text-2xl font-bold text-gray-800 mb-1">350</h5>
                            <small class="text-gray-500">Optimal: 300+</small>
                            <div class="w-full bg-purple-200 rounded-full h-2 mt-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-card p-6 rounded-2xl">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-alt text-xl text-white"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800">Seasonal Planning</h4>
                    </div>
                    <div class="mb-6">
                        <h6 class="text-2xl font-bold text-blue-600 mb-2">Current Season: Spring</h6>
                        <div class="season-badge bg-green-100 text-green-800 border border-green-300">
                            <i class="fas fa-seedling"></i>
                            <span>Planting Season</span>
                        </div>
                        <p class="mt-3 text-gray-600">Optimal time to plant wheat crops</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                            <h6 class="text-green-600 font-bold mb-2 flex items-center">
                                <i class="fas fa-seedling mr-2"></i>Planting Season
                            </h6>
                            <p class="text-gray-600 text-sm">March - April</p>
                        </div>
                        <div class="p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                            <h6 class="text-yellow-600 font-bold mb-2 flex items-center">
                                <i class="fas fa-scissors mr-2"></i>Harvest Season
                            </h6>
                            <p class="text-gray-600 text-sm">July - August</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity and Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="content-card p-6 rounded-2xl md:col-span-2">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-activity text-xl text-white"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800">Recent Activity</h4>
                    </div>
                    <div class="space-y-2">
                        <div class="activity-item">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-seedling text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Wheat field #3 planted successfully</p>
                                        <small class="text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1"></i>Mar 15, 2024 14:30
                                        </small>
                                    </div>
                                </div>
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">New</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-cart-arrow-down text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Order #1247 received from ABC Foods</p>
                                        <small class="text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1"></i>Mar 14, 2024 09:15
                                        </small>
                                    </div>
                                </div>
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">New</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-scissors text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Harvest completed for field #1</p>
                                        <small class="text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1"></i>Mar 13, 2024 16:45
                                        </small>
                                    </div>
                                </div>
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium">Done</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-card p-6 rounded-2xl">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-bolt text-xl text-white"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800">Quick Actions</h4>
                    </div>
                    <div class="flex flex-col gap-4">
                        <a href="#" class="btn-modern flex items-center justify-center group">
                            <i class="fas fa-plus-circle mr-2 group-hover:scale-110 transition-transform"></i>
                            Record Harvest
                        </a>
                        <a href="#" class="btn-modern flex items-center justify-center group">
                            <i class="fas fa-cart-plus mr-2 group-hover:scale-110 transition-transform"></i>
                            Place Order
                        </a>
                        <a href="#" class="btn-modern flex items-center justify-center group">
                            <i class="fas fa-calendar-plus mr-2 group-hover:scale-110 transition-transform"></i>
                            Plan Crop
                        </a>
                        <a href="#" class="btn-secondary-modern flex items-center justify-center group">
                            <i class="fas fa-chart-line mr-2 group-hover:scale-110 transition-transform"></i>
                            View Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>