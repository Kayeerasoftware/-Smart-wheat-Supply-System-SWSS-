@extends('layouts.app')

@section('content')
<style>
/* Custom styles for enhanced dashboard */
.dashboard-wrapper {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.sidebar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border-radius: 0 20px 20px 0;
    border: none;
    color: #2d3748 !important;
}

.sidebar .nav-link {
    color: #4a5568 !important;
    padding: 12px 20px;
    margin: 4px 10px;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.sidebar .nav-link:hover {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white !important;
    transform: translateX(5px);
}

.sidebar .nav-link.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white !important;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.sidebar h4 {
    color: #2d3748;
    font-weight: 700;
    margin-bottom: 5px;
}

.main-content {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px 0 0 20px;
    padding: 0;
    overflow: hidden;
}

.top-nav {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    margin: 20px;
    padding: 15px 25px;
}

.stats-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: none;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.stats-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stats-card .card-body {
    padding: 25px;
}

.stats-card h5 {
    color: #718096;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.stats-card h2 {
    color: #2d3748;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.stats-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 2rem;
    opacity: 0.3;
    color: #667eea;
}

.content-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: none;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.content-card .card-header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    border-radius: 20px 20px 0 0;
    padding: 20px 25px;
    font-weight: 600;
    font-size: 1.1rem;
}

.content-card .card-body {
    padding: 25px;
}

.activity-item {
    padding: 15px 0;
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.activity-item:hover {
    background: rgba(102, 126, 234, 0.05);
    border-radius: 10px;
    padding-left: 15px;
    margin: 0 -15px;
}

.activity-item:last-child {
    border-bottom: none;
}

.btn-modern {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    border-radius: 12px;
    padding: 12px 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
    display: block;
    text-align: center;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
    color: white;
    text-decoration: none;
}

.btn-secondary-modern {
    background: rgba(255, 255, 255, 0.9);
    border: 2px solid #e2e8f0;
    color: #4a5568;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-secondary-modern:hover {
    background: white;
    border-color: #667eea;
    color: #667eea;
}

.user-badge {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.welcome-text {
    color: #4a5568;
    font-weight: 600;
    margin-right: 15px;
}

.logistics-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

.delivery-card {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .dashboard-wrapper {
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    }
    
    .sidebar {
        border-radius: 0;
    }
    
    .main-content {
        border-radius: 0;
    }
    
    .top-nav {
        margin: 10px;
        border-radius: 10px;
    }
}
</style>

<div class="dashboard-wrapper">
    <div class="container-fluid">
    <div class="row">
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky py-4">
                <div class="text-center mb-4">
                    <h4>SCM</h4>
                        <p class="text-muted small mb-0">Wheat Supply Chain</p>
                        <div style="width: 40px; height: 3px; background: linear-gradient(90deg, #667eea, #764ba2); margin: 10px auto; border-radius: 2px;"></div>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                            <a class="nav-link active" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-truck me-2"></i>Logistics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-calendar-check me-2"></i>Delivery Schedule
                            </a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-boxes me-2"></i>Inventory
                            </a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-graph-up me-2"></i>Analytics
                            </a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-file-earmark-text me-2"></i>Reports
                            </a>
                    </li>
                    <li class="nav-item mt-4">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                                <button type="submit" class="nav-link text-danger bg-transparent border-0 w-100 text-start">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

            <main class="col-md-10 ms-sm-auto main-content">
                <nav class="top-nav d-flex justify-content-between align-items-center">
                <div>
                        <h4 class="mb-0" style="color: #2d3748; font-weight: 700;">Distributor Dashboard</h4>
                        <small class="text-muted">Manage logistics, routes, and deliveries</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="welcome-text">Welcome, {{ Auth::user()->username }}</span>
                        <span class="user-badge">Distributor</span>
                </div>
            </nav>

                <div class="px-4 pb-4">
                    <!-- Logistics and Delivery Row -->
            <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="logistics-card">
                                <h5 class="mb-2">Logistics Management</h5>
                                <h3 class="mb-2">Active Routes: {{ $activeRoutes ?? 8 }}</h3>
                                <p class="mb-2">Average delivery time: 2.3 hours</p>
                                <div class="d-flex justify-content-between">
                                    <span>Efficiency: 96%</span>
                                    <span class="text-success">+3%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="delivery-card">
                                <h5 class="mb-2">Today's Deliveries</h5>
                                <h3 class="mb-2">{{ $todayDeliveries ?? 15 }} Scheduled</h3>
                                <p class="mb-2">{{ $completedDeliveries ?? 8 }} completed</p>
                                <div class="d-flex justify-content-between">
                                    <span>On Time: 93%</span>
                                    <span class="text-success">+5%</span>
                    </div>
                </div>
                        </div>
                    </div>

                    <!-- Stats Row -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-cart-check stats-icon"></i>
                                    <h5>Active Orders</h5>
                                    <h2>{{ $activeOrders ?? 24 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-truck stats-icon"></i>
                            <h5>Today's Deliveries</h5>
                                    <h2>{{ $todayDeliveries ?? 15 }}</h2>
                        </div>
                    </div>
                </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-exclamation-triangle stats-icon"></i>
                            <h5>Low Stock Items</h5>
                                    <h2>{{ $lowStockItems ?? 3 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-boxes stats-icon"></i>
                            <h5>Total Inventory</h5>
                                    <h2>{{ $totalInventory ?? 450 }}<small style="font-size: 1rem; color: #718096;"> tons</small></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                            <div class="card content-card">
                                <div class="card-header">
                                    <i class="bi bi-activity me-2"></i>Delivery Activity
                                </div>
                        <div class="card-body">
                            @forelse ($recentActivity as $activity)
                                <div class="activity-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <p class="mb-1" style="color: #2d3748; font-weight: 500;">{{ $activity->description }}</p>
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i>{{ $activity->created_at->format('M d, Y H:i') }}
                                                    </small>
                                                </div>
                                                <span class="badge bg-light text-dark">In Transit</span>
                                            </div>
                                </div>
                            @empty
                                        <div class="text-center py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3; color: #667eea;"></i>
                                            <p class="text-muted mt-3 mb-0">No recent activity</p>
                                            <small class="text-muted">Your delivery activities will appear here</small>
                                        </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                            <div class="card content-card">
                                <div class="card-header">
                                    <i class="bi bi-lightning me-2"></i>Quick Actions
                                </div>
                        <div class="card-body">
                                    <div class="d-grid gap-3">
                                        <a href="#" class="btn-modern">
                                            <i class="bi bi-route me-2"></i>Optimize Routes
                                        </a>
                                        <a href="#" class="btn-modern">
                                            <i class="bi bi-calendar-plus me-2"></i>Schedule Delivery
                                        </a>
                                        <a href="#" class="btn-modern">
                                            <i class="bi bi-boxes me-2"></i>Allocate Inventory
                                        </a>
                                        <a href="#" class="btn-secondary-modern">
                                            <i class="bi bi-graph-up me-2"></i>View Analytics
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            </div>
    </div>
</div>
@endsection 