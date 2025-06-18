@extends('layouts.app')

@section('content')
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
                </div>
                
                <ul class="nav flex-column pt-3">
                    <li class="nav-item">
                        <a class="nav-link active bg-primary rounded mx-2 mb-1" href="{{ route('manufacturer.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
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

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-4 py-3">
            <!-- Top Navigation Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Manufacturer Dashboard</h2>
                    <p class="text-muted mb-0">{{ now()->format('l, F j, Y') }}</p>
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
                        </div>
                    </div>
                </div>
            </div>

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
                            @endforelse
                        </div>
                    </div>
                </div>

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
                        </div>
                    </div>
                </div>
            </div>

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