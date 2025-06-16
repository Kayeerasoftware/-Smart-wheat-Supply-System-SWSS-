@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-dark sidebar text-white">
            <div class="sidebar-sticky">
                <div class="text-center mb-4">
                    <h4>SCM</h4>
                    <p class="text-muted small">Wheat Supply Chain</p>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active text-white" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">My Crops</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">Weather</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">Market Prices</a>
                    </li>
                    <li class="nav-item mt-4">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link text-danger bg-transparent border-0">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-10 ms-sm-auto px-4">
            <nav class="navbar navbar-light bg-light mb-4">
                <span class="navbar-brand">Farmer Dashboard</span>
                <div>
                    <span class="me-3">Welcome, {{ Auth::user()->username }}</span>
                    <span class="badge bg-success">Farmer</span>
                </div>
            </nav>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5>Active Crops</h5>
                            <h2>{{ $activeCrops ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5>Harvest Ready</h5>
                            <h2>{{ $harvestReady ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5>Pending Orders</h5>
                            <h2>{{ $pendingOrders ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5>Market Price</h5>
                            <h2>${{ $marketPrice ?? '0.00' }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Recent Activity</div>
                        <div class="card-body">
                            @forelse ($recentActivity as $activity)
                                <div class="activity-item">
                                    <p>{{ $activity->description }}</p>
                                    <small class="text-muted">{{ $activity->created_at->format('M d, Y H:i') }}</small>
                                </div>
                            @empty
                                <p class="text-muted">No recent activity</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Quick Actions</div>
                        <div class="card-body">
                            <a href="#" class="btn btn-primary mb-2">Add New Crop</a>
                            <a href="#" class="btn btn-warning mb-2">View Orders</a>
                            <a href="#" class="btn btn-info mb-2">Check Weather</a>
                            <a href="#" class="btn btn-secondary mb-2">Market Analysis</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection 