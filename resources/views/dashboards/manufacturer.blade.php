@extends('layouts.app')

@section('content')
<<<<<<< HEAD
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

.production-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

.quality-card {
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
=======
<div class="container-fluid">
    <div class="row">
        <!-- Enhanced Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-dark sidebar">
            <div class="sidebar-sticky">
                <div class="text-center py-4 border-bottom border-secondary">
                    <div class="mb-2">
                        <i class="bi bi-truck text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-white mb-1">WheatSCM</h4>
                    <p class="text-muted small mb-0">Supply Chain Management</p>
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
                </div>
                
                <ul class="nav flex-column pt-3">
                    <li class="nav-item">
<<<<<<< HEAD
                            <a class="nav-link active" href="{{ route('dashboard') }}">
=======
                        <a class="nav-link active bg-primary rounded mx-2 mb-1" href="{{ route('manufacturer.dashboard') }}">
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
<<<<<<< HEAD
                            <a class="nav-link" href="#">
                                <i class="bi bi-gear-wide-connected me-2"></i>Production
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-shield-check me-2"></i>Quality Control
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-boxes me-2"></i>Batch Management
                            </a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-truck me-2"></i>Distribution
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
=======
                        <a class="nav-link text-light hover-bg-secondary rounded mx-2 mb-1" href="{{ route('manufacturer.production-lines') }}">
                            <i class="bi bi-gear me-2"></i>Production Lines
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light hover-bg-secondary rounded mx-2 mb-1" href="{{ route('manufacturer.raw-materials') }}">
                            <i class="bi bi-boxes me-2"></i>Raw Materials
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light hover-bg-secondary rounded mx-2 mb-1" href="{{ route('manufacturer.quality-checks') }}">
                            <i class="bi bi-shield-check me-2"></i>Quality Control
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light hover-bg-secondary rounded mx-2 mb-1" href="#">
                            <i class="bi bi-graph-up me-2"></i>Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light hover-bg-secondary rounded mx-2 mb-1" href="#">
                            <i class="bi bi-gear-fill me-2"></i>Settings
                        </a>
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
                    </li>
                </ul>

                <div class="mt-auto p-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

<<<<<<< HEAD
            <main class="col-md-10 ms-sm-auto main-content">
                <nav class="top-nav d-flex justify-content-between align-items-center">
                <div>
                        <h4 class="mb-0" style="color: #2d3748; font-weight: 700;">Manufacturer Dashboard</h4>
                        <small class="text-muted">Manage production, quality control, and distribution</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="welcome-text">Welcome, {{ Auth::user()->username }}</span>
                        <span class="user-badge">Manufacturer</span>
=======
        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-4 py-3">
            <!-- Top Navigation Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Manufacturer Dashboard</h2>
                    <p class="text-muted mb-0">{{ now()->format('l, F j, Y') }}</p>
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-bell text-muted me-2"></i>
                        @if(isset($notifications) && $notifications->count() > 0)
                            <span class="badge bg-danger">{{ $notifications->count() }}</span>
                        @endif
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>{{ Auth::user()->username }}
                            <span class="badge bg-warning ms-2">Manufacturer</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

<<<<<<< HEAD
                <div class="px-4 pb-4">
                    <!-- Production and Quality Row -->
            <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="production-card">
                                <h5 class="mb-2">Production Planning</h5>
                                <h3 class="mb-2">Active Lines: {{ $activeLines ?? 4 }}</h3>
                                <p class="mb-2">Daily output target: {{ $dailyOutput ?? 120 }} tons</p>
                                <div class="d-flex justify-content-between">
                                    <span>Efficiency: 94%</span>
                                    <span class="text-success">+2%</span>
=======
            <!-- Status Cards -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                        <i class="bi bi-gear-fill text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Active Production Lines</p>
                                    <h3 class="mb-0">{{ $activeLines ?? 0 }}</h3>
                                    <small class="text-success">
                                        <i class="bi bi-arrow-up"></i> 
                                        {{ $linesChangePercent ?? 0 }}% from yesterday
                                    </small>
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="quality-card">
                                <h5 class="mb-2">Quality Control</h5>
                                <h3 class="mb-2">Pass Rate: 98.5%</h3>
                                <p class="mb-2">Last 24 hours quality metrics</p>
                                <div class="d-flex justify-content-between">
                                    <span>Issues: {{ $qualityIssues ?? 2 }}</span>
                                    <span class="text-warning">-15%</span>
                    </div>
                </div>
<<<<<<< HEAD
                        </div>
                    </div>

                    <!-- Stats Row -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-gear-wide-connected stats-icon"></i>
                                    <h5>Active Lines</h5>
                                    <h2>{{ $activeLines ?? 4 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-arrow-up-circle stats-icon"></i>
                            <h5>Daily Output</h5>
                                    <h2>{{ $dailyOutput ?? 120 }}<small style="font-size: 1rem; color: #718096;"> tons</small></h2>
                        </div>
                    </div>
                </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-exclamation-triangle stats-icon"></i>
                            <h5>Quality Issues</h5>
                                    <h2>{{ $qualityIssues ?? 2 }}</h2>
                        </div>
                    </div>
                </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-box-seam stats-icon"></i>
                            <h5>Raw Materials</h5>
                                    <h2>{{ $rawMaterials ?? 85 }}<small style="font-size: 1rem; color: #718096;"> tons</small></h2>
=======

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                        <i class="bi bi-graph-up text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Daily Output</p>
                                    <h3 class="mb-0">{{ number_format($dailyOutput ?? 0) }} <small class="text-muted fs-6">tons</small></h3>
                                    <small class="text-success">
                                        <i class="bi bi-arrow-up"></i> 
                                        Target: {{ number_format($dailyTarget ?? 0) }} tons
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                        <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Quality Issues</p>
                                    <h3 class="mb-0">{{ $qualityIssues ?? 0 }}</h3>
                                    <small class="{{ ($qualityIssues ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                        @if(($qualityIssues ?? 0) > 0)
                                            <i class="bi bi-arrow-up"></i> Needs attention
                                        @else
                                            <i class="bi bi-check-circle"></i> All clear
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                        <i class="bi bi-boxes text-info fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Raw Materials Stock</p>
                                    <h3 class="mb-0">{{ number_format($rawMaterials ?? 0) }} <small class="text-muted fs-6">tons</small></h3>
                                    <small class="{{ ($rawMaterials ?? 0) < 100 ? 'text-warning' : 'text-success' }}">
                                        @if(($rawMaterials ?? 0) < 100)
                                            <i class="bi bi-exclamation-circle"></i> Low stock
                                        @else
                                            <i class="bi bi-check-circle"></i> Adequate
                                        @endif
                                    </small>
                                </div>
                            </div>
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
                        </div>
                    </div>
                </div>
            </div>

<<<<<<< HEAD
            <div class="row">
                <div class="col-md-8">
                            <div class="card content-card">
                                <div class="card-header">
                                    <i class="bi bi-activity me-2"></i>Production Activity
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
                                                <span class="badge bg-light text-dark">Active</span>
                                            </div>
                                </div>
                            @empty
                                        <div class="text-center py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3; color: #667eea;"></i>
                                            <p class="text-muted mt-3 mb-0">No recent activity</p>
                                            <small class="text-muted">Your production activities will appear here</small>
                                        </div>
=======
            <!-- Main Content Grid -->
            <div class="row g-4">
                <!-- Recent Activity -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Activity</h5>
                                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            @forelse ($recentActivity ?? [] as $activity)
                                <div class="d-flex activity-item py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                            <i class="bi bi-{{ $activity->icon ?? 'circle' }} text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-1">{{ $activity->description }}</p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $activity->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    @if(isset($activity->status))
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-{{ $activity->status === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($activity->status) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">No recent activity to display</p>
                                </div>
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
                            @endforelse
                        </div>
                    </div>
                </div>
<<<<<<< HEAD
                <div class="col-md-4">
                            <div class="card content-card">
                                <div class="card-header">
                                    <i class="bi bi-lightning me-2"></i>Quick Actions
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-3">
                                        <a href="#" class="btn-modern">
                                            <i class="bi bi-play-circle me-2"></i>Start Production
                                        </a>
                                        <a href="#" class="btn-modern">
                                            <i class="bi bi-shield-check me-2"></i>Quality Check
                                        </a>
                                        <a href="#" class="btn-modern">
                                            <i class="bi bi-boxes me-2"></i>Manage Batches
                                        </a>
                                        <a href="#" class="btn-secondary-modern">
                                            <i class="bi bi-truck me-2"></i>Schedule Delivery
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Management Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card content-card">
                                <div class="card-header">
                                    <i class="bi bi-people me-2"></i>Approved Suppliers
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Supplier Name</th>
                                                            <th>Business Type</th>
                                                            <th>Contact</th>
                                                            <th>Rating</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($approvedSuppliers ?? [] as $supplier)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-sm me-3">
                                                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                                <span class="fw-bold">{{ substr($supplier->username, 0, 2) }}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-0">{{ $supplier->username }}</h6>
                                                                            <small class="text-muted">{{ $supplier->email }}</small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @if($supplier->vendor && $supplier->vendor->application_data)
                                                                        <span class="badge bg-info">{{ ucfirst($supplier->vendor->application_data['business_type'] ?? 'N/A') }}</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">N/A</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div>
                                                                        <small class="text-muted">Phone: {{ $supplier->phone ?? 'N/A' }}</small><br>
                                                                        <small class="text-muted">Address: {{ $supplier->address ?? 'N/A' }}</small>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @if($supplier->vendor && $supplier->vendor->total_score)
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="me-2">
                                                                                <i class="bi bi-star-fill text-warning"></i>
                                                                                <span class="fw-bold">{{ number_format($supplier->vendor->total_score / 20, 1) }}</span>
                                                                            </div>
                                                                            <small class="text-muted">({{ $supplier->vendor->total_score }}%)</small>
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted">No rating</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-success">Active</span>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group" role="group">
                                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                                onclick="openChat({{ $supplier->user_id }}, '{{ $supplier->username }}')">
                                                                            <i class="bi bi-chat-dots me-1"></i>Chat
                                                                        </button>
                                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                                onclick="viewSupplierDetails({{ $supplier->user_id }})">
                                                                            <i class="bi bi-eye me-1"></i>View
                                                                        </button>
                                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                                onclick="requestQuote({{ $supplier->user_id }})">
                                                                            <i class="bi bi-calculator me-1"></i>Quote
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center py-4">
                                                                    <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3; color: #667eea;"></i>
                                                                    <p class="text-muted mt-3 mb-0">No approved suppliers found</p>
                                                                    <small class="text-muted">Approved suppliers will appear here for collaboration</small>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card" style="background: rgba(255, 255, 255, 0.9); border: none; border-radius: 15px;">
                                                <div class="card-header" style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; border: none; border-radius: 15px 15px 0 0;">
                                                    <h6 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Quick Chat</h6>
                                                </div>
                        <div class="card-body">
                                                    <div id="chatContainer" style="height: 300px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; background: white;">
                                                        <div class="text-center text-muted">
                                                            <i class="bi bi-chat-dots" style="font-size: 2rem; opacity: 0.5;"></i>
                                                            <p class="mt-2 mb-0">Select a supplier to start chatting</p>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3" id="chatInputContainer" style="display: none;">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="chatMessage" placeholder="Type your message...">
                                                            <button class="btn btn-primary" type="button" onclick="sendMessage()">
                                                                <i class="bi bi-send"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
=======

                <!-- Quick Actions & Alerts -->
                <div class="col-lg-4">
                    <!-- Quick Actions -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('manufacturer.orders.create') }}" class="btn btn-primary">
                                    <i class="bi bi-play-circle me-2"></i>Create Order
                                </a>
                                <a href="{{ route('manufacturer.quality-checks.create') }}" class="btn btn-warning">
                                    <i class="bi bi-shield-check me-2"></i>Quality Check
                                </a>
                                <a href="{{ route('manufacturer.raw-materials') }}" class="btn btn-info">
                                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Raw Materials
                                </a>
                                <a href="{{ route('manufacturer.production-lines.create') }}" class="btn btn-secondary">
                                    <i class="bi bi-tools me-2"></i>Add Production Line
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- System Alerts -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">System Alerts</h5>
                                @if(isset($alerts) && $alerts->count() > 0)
                                    <span class="badge bg-danger">{{ $alerts->count() }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @forelse ($alerts ?? [] as $alert)
                                <div class="alert alert-{{ $alert->type }} alert-dismissible fade show" role="alert">
                                    <i class="bi bi-{{ $alert->icon }} me-2"></i>
                                    <strong>{{ $alert->title }}</strong><br>
                                    {{ $alert->message }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @empty
                                <div class="text-center py-3">
                                    <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2 mb-0">No active alerts</p>
                                </div>
                            @endforelse
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
                        </div>
                    </div>
                </div>
            </main>
            </div>
<<<<<<< HEAD
    </div>
</div>

<script>
let currentChatSupplier = null;

function openChat(supplierId, supplierName) {
    currentChatSupplier = supplierId;
    
    // Update chat container
    const chatContainer = document.getElementById('chatContainer');
    const chatInputContainer = document.getElementById('chatInputContainer');
    
    chatContainer.innerHTML = `
        <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
            <div class="avatar-sm me-2">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                    <span class="fw-bold small">${supplierName.substring(0, 2)}</span>
                </div>
            </div>
            <div>
                <h6 class="mb-0">${supplierName}</h6>
                <small class="text-success">Online</small>
            </div>
        </div>
        <div id="chatMessages" style="height: 200px; overflow-y: auto;">
            <div class="text-center text-muted">
                <small>Start your conversation with ${supplierName}</small>
            </div>
        </div>
    `;
    
    chatInputContainer.style.display = 'block';
    document.getElementById('chatMessage').focus();
}

function sendMessage() {
    const messageInput = document.getElementById('chatMessage');
    const message = messageInput.value.trim();
    
    if (message && currentChatSupplier) {
        const chatMessages = document.getElementById('chatMessages');
        const timestamp = new Date().toLocaleTimeString();
        
        // Add message to chat
        const messageHtml = `
            <div class="d-flex justify-content-end mb-2">
                <div class="bg-primary text-white p-2 rounded" style="max-width: 70%;">
                    <div class="small">${message}</div>
                    <small class="opacity-75">${timestamp}</small>
                </div>
            </div>
        `;
        
        chatMessages.innerHTML += messageHtml;
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Clear input
        messageInput.value = '';
        
        // Simulate supplier response (in real app, this would be via WebSocket)
        setTimeout(() => {
            const responses = [
                "Thank you for your message. I'll get back to you shortly.",
                "I understand your requirements. Let me check our inventory.",
                "We can definitely help with that. What's your timeline?",
                "Great! I'll prepare a quote for you right away."
            ];
            const randomResponse = responses[Math.floor(Math.random() * responses.length)];
            
            const responseHtml = `
                <div class="d-flex justify-content-start mb-2">
                    <div class="bg-light p-2 rounded" style="max-width: 70%;">
                        <div class="small">${randomResponse}</div>
                        <small class="text-muted">${new Date().toLocaleTimeString()}</small>
                    </div>
                </div>
            `;
            
            chatMessages.innerHTML += responseHtml;
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 2000);
    }
}

function viewSupplierDetails(supplierId) {
    // TODO: Implement supplier details modal
    alert('View supplier details for ID: ' + supplierId);
}

function requestQuote(supplierId) {
    // TODO: Implement quote request functionality
    alert('Request quote from supplier ID: ' + supplierId);
}

// Handle Enter key in chat input
document.addEventListener('DOMContentLoaded', function() {
    const chatMessage = document.getElementById('chatMessage');
    if (chatMessage) {
        chatMessage.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
});
</script>
@endsection 
=======

            <!-- Production Chart (Optional) -->
            @if(isset($chartData))
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0">Production Trends</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="productionChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </main>
    </div>
</div>

@push('styles')
<style>
    .sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 100;
        padding: 0;
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    }

    .sidebar-sticky {
        position: sticky;
        top: 0;
        height: 100vh;
        display: flex;
        flex-direction: column;
        padding-top: 0;
        overflow-x: hidden;
        overflow-y: auto;
    }

    .nav-link {
        color: rgba(255, 255, 255, .75);
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        color: rgba(255, 255, 255, 1);
        background-color: rgba(255, 255, 255, .1);
    }

    .nav-link.active {
        color: white;
    }

    .card {
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .activity-item {
        transition: background-color 0.2s ease;
    }

    .activity-item:hover {
        background-color: rgba(0, 0, 0, .02);
    }

    @media (max-width: 767.98px) {
        .sidebar {
            top: 0;
        }
    }
</style>
@endpush

@push('scripts')
@if(isset($chartData))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Production Chart
    const ctx = document.getElementById('productionChart').getContext('2d');
    const productionChart = new Chart(ctx, {
        type: 'line',
        data: {!! json_encode($chartData) !!},
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Daily Production Output'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Tons'
                    }
                }
            }
        }
    });
</script>
@endif
@endpush
@endsection
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
