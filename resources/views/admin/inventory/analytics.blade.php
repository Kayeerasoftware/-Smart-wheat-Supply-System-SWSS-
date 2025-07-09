<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Analytics - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%); min-height: 100vh; }
        .font-space { font-family: 'Space Grotesk', sans-serif; }
        .glass-card { background: rgba(255,255,255,0.08); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; }
        .sidebar { background: rgba(0,0,0,0.4); backdrop-filter: blur(20px); border-right: 1px solid rgba(255,255,255,0.1); position: fixed; top: 5rem; left: 0; height: 100vh; z-index: 40; overflow-y: auto; }
        .sidebar-item { transition: all 0.3s; position: relative; overflow: hidden; }
        .sidebar-item.active, .sidebar-item:hover { color: white; transform: translateX(5px); }
        .fixed-nav { position: fixed; top: 0; left: 0; right: 0; z-index: 50; background: rgba(0,0,0,0.4); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .main-content { margin-left: 16rem; margin-top: 5rem; min-height: calc(100vh - 5rem); }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); position: fixed; z-index: 50; height: 100vh; } .sidebar.open { transform: translateX(0); } .main-content { margin-left: 0; margin-top: 5rem; } }
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
                <p class="text-xs text-gray-400">Inventory Analytics</p>
            </div>
        </div>
        <div class="flex items-center space-x-6">
            <div class="relative">
                <i class="fas fa-bell text-gray-300 text-xl cursor-pointer hover:text-white transition-colors"></i>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm font-semibold">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-400">Administrator</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name ?? 'A', 0, 2) }}</span>
                </div>
            </div>
        </div>
    </nav>
    <!-- Sidebar -->
    <aside class="sidebar w-64 p-6" id="sidebar">
        <nav class="space-y-2">
            <a href="{{ route('dashboard') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.vendors') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                <i class="fas fa-building w-5"></i>
                <span class="font-medium">Vendor Management</span>
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                <i class="fas fa-wheat-awn w-5"></i>
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
    <main class="main-content p-6">
        <div class="mb-4">
            <button onclick="window.history.back()" class="glass-card px-5 py-2 rounded-lg flex items-center text-white hover:bg-white/10 transition mb-4">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </button>
        </div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold font-space gradient-text mb-2">Inventory Analytics</h1>
            <p class="text-gray-400">Key statistics and insights for inventory management</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-white mb-2">Total Inventory Value</h3>
                <p class="text-2xl font-bold text-blue-300 mb-1">${{ number_format($analytics['total_inventory_value'] ?? 0, 2) }}</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-white mb-2">Low Stock Items</h3>
                <p class="text-2xl font-bold text-yellow-300 mb-1">{{ $analytics['low_stock_items'] ?? 0 }}</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-white mb-2">Out of Stock Items</h3>
                <p class="text-2xl font-bold text-red-400 mb-1">{{ $analytics['out_of_stock_items'] ?? 0 }}</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-white mb-2">Total Products</h3>
                <p class="text-2xl font-bold text-green-300 mb-1">{{ $analytics['total_products'] ?? 0 }}</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-white mb-2">Total Warehouses</h3>
                <p class="text-2xl font-bold text-purple-300 mb-1">{{ $analytics['total_warehouses'] ?? 0 }}</p>
            </div>
        </div>
    </main>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });
    </script>
</body>
</html> 