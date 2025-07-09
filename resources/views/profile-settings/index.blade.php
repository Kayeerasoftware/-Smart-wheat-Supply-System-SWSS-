<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Supplier Dashboard - Profile Settings</title>
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

        .form-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.1);
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background: var(--accent-gradient);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .certification-badge {
            background: rgba(79, 172, 254, 0.2);
            color: #60a5fa;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
            margin: 0.25rem;
        }

        .specialization-badge {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
            margin: 0.25rem;
        }

        .progress-ring {
            transform: rotate(-90deg);
        }

        .progress-ring-circle {
            transition: stroke-dashoffset 0.35s;
            transform-origin: 50% 50%;
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
            <div class="relative">
                <i class="fas fa-bell text-gray-300 text-xl cursor-pointer hover:text-white transition-colors"></i>
                <span class="notification-dot absolute -top-1 -right-1 w-3 h-3 rounded-full"></span>
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

    <div class="flex">
        <!-- Sidebar -->
        <aside class="sidebar w-64 p-6" id="sidebar">
            <nav class="space-y-2">
                <a href="/supplier/dashboard" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="/inventory" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-seedling w-5"></i>
                    <span class="font-medium">Inventory</span>
                </a>
                <a href="/orders" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
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
                <a href="/profile-settings" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-user-cog w-5"></i>
                    <span class="font-medium">Profile Settings</span>
                </a>
                
                <div class="pt-6 mt-6 border-t border-gray-700">
                    <form action="/logout" method="POST">
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
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold font-space gradient-text">Profile Settings</h1>
                        <p class="mt-2 text-sm text-gray-400">Manage your account and preferences</p>
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit" form="profile-form" class="btn-primary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
                            <i class="fas fa-save mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="glass-card p-4 mb-6 border-l-4 border-green-500 bg-green-500/10">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 mr-3"></i>
                        <p class="text-green-400">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="glass-card p-4 mb-6 border-l-4 border-red-500 bg-red-500/10">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                        <p class="text-red-400">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="glass-card p-4 mb-6 border-l-4 border-red-500 bg-red-500/10">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                        <p class="text-red-400 font-medium">Please fix the following errors:</p>
                    </div>
                    <ul class="list-disc list-inside text-red-400 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Account Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card p-6 rounded-2xl text-center float-animation">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-cart text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $profileData['account_stats']['total_orders'] ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Total Orders</p>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.2s">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-handshake text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $profileData['account_stats']['total_contracts'] ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Active Contracts</p>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.4s">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-star text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $profileData['account_stats']['average_rating'] ?? 0 }}</h3>
                    <p class="text-gray-400 mb-2">Average Rating</p>
                </div>

                <div class="stat-card p-6 rounded-2xl text-center float-animation" style="animation-delay: 0.6s">
                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-truck text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold font-space mb-2">{{ $profileData['account_stats']['on_time_delivery'] ?? 0 }}%</h3>
                    <p class="text-gray-400 mb-2">On-Time Delivery</p>
                </div>
            </div>

            <!-- Profile Sections -->
            <form id="profile-form" method="POST" action="{{ route('profile-settings.save') }}" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @csrf
                <!-- Personal Information -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold font-space">Personal Information</h3>
                        <button type="button" class="text-blue-400 hover:text-blue-300 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-4 mb-6">
                        <img src="{{ $profileData['personal_info']['avatar'] ?? 'https://ui-avatars.com/api/?name=User&background=667eea&color=fff&size=128' }}" 
                             alt="Profile Avatar" class="w-20 h-20 rounded-full border-4 border-blue-500/20">
                        <div>
                            <h4 class="text-lg font-semibold">{{ $profileData['personal_info']['name'] ?? 'User Name' }}</h4>
                            <p class="text-gray-400">{{ $profileData['personal_info']['position'] ?? 'Position' }}</p>
                            <p class="text-sm text-gray-500">Member since {{ \Carbon\Carbon::parse($profileData['account_stats']['member_since'] ?? now())->format('M Y') }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Full Name</label>
                            <input type="text" name="name" value="{{ $profileData['personal_info']['name'] ?? '' }}" 
                                   class="form-input w-full rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                            <input type="email" name="email" value="{{ $profileData['personal_info']['email'] ?? '' }}" 
                                   class="form-input w-full rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Phone</label>
                            <input type="tel" name="phone" value="{{ $profileData['personal_info']['phone'] ?? '' }}" 
                                   class="form-input w-full rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Address</label>
                            <textarea name="address" class="form-input w-full rounded-lg px-3 py-2" rows="3">{{ $profileData['personal_info']['address'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Bio</label>
                            <textarea name="bio" class="form-input w-full rounded-lg px-3 py-2" rows="3">{{ $profileData['personal_info']['bio'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold font-space">Business Information</h3>
                        <button type="button" class="text-blue-400 hover:text-blue-300 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Business Name</label>
                            <input type="text" name="business_name" value="{{ $profileData['business_info']['business_name'] ?? '' }}" 
                                   class="form-input w-full rounded-lg px-3 py-2">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Business Type</label>
                                <select name="business_type" class="form-input w-full rounded-lg px-3 py-2">
                                    <option value="Corporation" {{ ($profileData['business_info']['business_type'] ?? '') == 'Corporation' ? 'selected' : '' }}>Corporation</option>
                                    <option value="LLC" {{ ($profileData['business_info']['business_type'] ?? '') == 'LLC' ? 'selected' : '' }}>LLC</option>
                                    <option value="Partnership" {{ ($profileData['business_info']['business_type'] ?? '') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                    <option value="Sole Proprietorship" {{ ($profileData['business_info']['business_type'] ?? '') == 'Sole Proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Founded Year</label>
                                <input type="number" name="founded_year" value="{{ $profileData['business_info']['founded_year'] ?? '' }}" 
                                       class="form-input w-full rounded-lg px-3 py-2">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Tax ID</label>
                                <input type="text" name="tax_id" value="{{ $profileData['business_info']['tax_id'] ?? '' }}" 
                                       class="form-input w-full rounded-lg px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Registration Number</label>
                                <input type="text" name="registration_number" value="{{ $profileData['business_info']['registration_number'] ?? '' }}" 
                                       class="form-input w-full rounded-lg px-3 py-2">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Certifications</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($profileData['business_info']['certifications'] ?? [] as $cert)
                                    <span class="certification-badge">{{ $cert }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Specializations</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($profileData['business_info']['specializations'] ?? [] as $spec)
                                    <span class="specialization-badge">{{ $spec }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Preferences -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold font-space">Contact Preferences</h3>
                        <button type="button" class="text-blue-400 hover:text-blue-300 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium">Email Notifications</h4>
                                <p class="text-sm text-gray-400">Receive notifications via email</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="email_notifications" {{ ($profileData['contact_preferences']['email_notifications'] ?? false) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium">SMS Notifications</h4>
                                <p class="text-sm text-gray-400">Receive notifications via SMS</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="sms_notifications" {{ ($profileData['contact_preferences']['sms_notifications'] ?? false) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium">Push Notifications</h4>
                                <p class="text-sm text-gray-400">Receive push notifications</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="push_notifications" {{ ($profileData['contact_preferences']['push_notifications'] ?? false) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium">Marketing Emails</h4>
                                <p class="text-sm text-gray-400">Receive marketing communications</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="marketing_emails" {{ ($profileData['contact_preferences']['marketing_emails'] ?? false) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium">Order Updates</h4>
                                <p class="text-sm text-gray-400">Get notified about order status changes</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="order_updates" {{ ($profileData['contact_preferences']['order_updates'] ?? false) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium">Payment Reminders</h4>
                                <p class="text-sm text-gray-400">Receive payment due reminders</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="payment_reminders" {{ ($profileData['contact_preferences']['payment_reminders'] ?? false) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium">System Alerts</h4>
                                <p class="text-sm text-gray-400">Receive system-wide alerts</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="system_alerts" {{ ($profileData['contact_preferences']['system_alerts'] ?? false) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold font-space">Security Settings</h3>
                        <button type="button" class="text-blue-400 hover:text-blue-300 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium">Two-Factor Authentication</h4>
                                <p class="text-sm text-gray-400">Add an extra layer of security</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" {{ ($profileData['security_settings']['two_factor_enabled'] ?? false) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="border-t border-gray-700 pt-4">
                            <h4 class="font-medium mb-3">Recent Login Activity</h4>
                            <div class="space-y-3">
                                @foreach($profileData['security_settings']['login_history'] ?? [] as $login)
                                    <div class="flex items-center justify-between p-3 bg-gray-800/50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium">{{ $login['device'] }}</p>
                                            <p class="text-xs text-gray-400">{{ $login['ip'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm">{{ \Carbon\Carbon::parse($login['date'])->format('M d, H:i') }}</p>
                                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($login['date'])->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t border-gray-700 pt-4">
                            <h4 class="font-medium mb-3">Password</h4>
                            <p class="text-sm text-gray-400 mb-3">Last changed: {{ \Carbon\Carbon::parse($profileData['security_settings']['last_password_change'] ?? now())->format('M d, Y') }}</p>
                            <button type="button" onclick="openPasswordModal()" class="btn-primary inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm text-white">
                                <i class="fas fa-key mr-2"></i>
                                Change Password
                            </button>
                        </div>
                    </div>
                </div>
            </form>
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

        // Add hover effects to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Password modal functions
        function openPasswordModal() {
            document.getElementById('passwordModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Animate modal content
            setTimeout(() => {
                const modalContent = document.getElementById('modalContent');
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closePasswordModal() {
            const modalContent = document.getElementById('modalContent');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                document.getElementById('passwordModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
                
                // Reset form
                document.getElementById('passwordForm').reset();
                document.getElementById('strengthBar').style.width = '0%';
                document.getElementById('strengthBar').className = 'h-full bg-red-500 transition-all duration-300';
                document.getElementById('strengthText').textContent = 'Password strength: Weak';
                document.getElementById('matchText').textContent = '';
            }, 300);
        }

        // Password visibility toggle
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '_icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            let feedback = [];
            
            // Length check
            if (password.length >= 8) {
                strength += 25;
                feedback.push('Length ✓');
            } else {
                feedback.push('At least 8 characters');
            }
            
            // Uppercase check
            if (/[A-Z]/.test(password)) {
                strength += 25;
                feedback.push('Uppercase ✓');
            } else {
                feedback.push('Uppercase letter');
            }
            
            // Lowercase check
            if (/[a-z]/.test(password)) {
                strength += 25;
                feedback.push('Lowercase ✓');
            } else {
                feedback.push('Lowercase letter');
            }
            
            // Number check
            if (/\d/.test(password)) {
                strength += 25;
                feedback.push('Number ✓');
            } else {
                feedback.push('Number');
            }
            
            // Update strength bar
            strengthBar.style.width = strength + '%';
            
            // Update colors and text
            if (strength <= 25) {
                strengthBar.className = 'h-full bg-red-500 transition-all duration-300';
                strengthText.textContent = 'Password strength: Weak';
                strengthText.className = 'text-xs text-red-400 mt-1';
            } else if (strength <= 50) {
                strengthBar.className = 'h-full bg-orange-500 transition-all duration-300';
                strengthText.textContent = 'Password strength: Fair';
                strengthText.className = 'text-xs text-orange-400 mt-1';
            } else if (strength <= 75) {
                strengthBar.className = 'h-full bg-yellow-500 transition-all duration-300';
                strengthText.textContent = 'Password strength: Good';
                strengthText.className = 'text-xs text-yellow-400 mt-1';
            } else {
                strengthBar.className = 'h-full bg-green-500 transition-all duration-300';
                strengthText.textContent = 'Password strength: Strong';
                strengthText.className = 'text-xs text-green-400 mt-1';
            }
        }

        // Password match checker
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const matchText = document.getElementById('matchText');
            
            if (confirmPassword === '') {
                matchText.textContent = '';
                matchText.className = 'text-xs text-gray-400 mt-1';
            } else if (password === confirmPassword) {
                matchText.textContent = 'Passwords match ✓';
                matchText.className = 'text-xs text-green-400 mt-1';
            } else {
                matchText.textContent = 'Passwords do not match';
                matchText.className = 'text-xs text-red-400 mt-1';
            }
        }

        // Close modal when clicking outside
        document.getElementById('passwordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePasswordModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('passwordModal').classList.contains('hidden')) {
                closePasswordModal();
            }
        });
    </script>

    <!-- Password Change Modal -->
    <div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="glass-card w-full max-w-md p-6 rounded-2xl relative transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold font-space">Change Password</h3>
                    <button onclick="closePasswordModal()" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                @if(session('status') === 'password-updated')
                    <div class="glass-card p-4 mb-6 border-l-4 border-green-500 bg-green-500/10">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <p class="text-green-400">Password updated successfully!</p>
                        </div>
                    </div>
                @endif

                @if($errors->updatePassword->any())
                    <div class="glass-card p-4 mb-6 border-l-4 border-red-500 bg-red-500/10">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                            <p class="text-red-400 font-medium">Please fix the following errors:</p>
                        </div>
                        <ul class="list-disc list-inside text-red-400 text-sm">
                            @foreach($errors->updatePassword->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4" id="passwordForm">
                    @csrf
                    @method('put')
                    
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-300 mb-1">Current Password</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" required
                                   class="form-input w-full rounded-lg px-3 py-2 pr-10" 
                                   placeholder="Enter your current password">
                            <button type="button" onclick="togglePasswordVisibility('current_password')" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                <i class="fas fa-eye" id="current_password_icon"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-1">New Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                   class="form-input w-full rounded-lg px-3 py-2 pr-10" 
                                   placeholder="Enter your new password"
                                   onkeyup="checkPasswordStrength(this.value)">
                            <button type="button" onclick="togglePasswordVisibility('password')" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                <i class="fas fa-eye" id="password_icon"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <div class="flex space-x-1">
                                <div class="flex-1 h-2 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-500 transition-all duration-300" id="strengthBar"></div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1" id="strengthText">Password strength: Weak</p>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="form-input w-full rounded-lg px-3 py-2 pr-10" 
                                   placeholder="Confirm your new password"
                                   onkeyup="checkPasswordMatch()">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation')" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                <i class="fas fa-eye" id="password_confirmation_icon"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1" id="matchText"></p>
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closePasswordModal()" 
                                class="flex-1 px-4 py-2 rounded-lg font-semibold text-sm text-gray-300 border border-gray-600 hover:border-gray-500 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="btn-primary flex-1 px-4 py-2 rounded-lg font-semibold text-sm text-white">
                            <i class="fas fa-key mr-2"></i>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 