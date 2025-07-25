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
                    </div>
                    
                    <ul class="nav flex-column pt-3">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('manufacturer.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('manufacturer.production-lines') }}">
                                <i class="bi bi-gear-wide-connected me-2"></i>Production Lines
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('manufacturer.quality-checks') }}">
                                <i class="bi bi-shield-check me-2"></i>Quality Control
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('manufacturer.raw-materials') }}">
                                <i class="bi bi-boxes me-2"></i>Raw Materials
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
                        <h4 class="mb-0" style="color: #2d3748; font-weight: 700;">Manufacturer Dashboard</h4>
                        <small class="text-muted">Manage production, quality control, and distribution</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="welcome-text">Welcome, {{ Auth::user()->username }}</span>
                        <span class="user-badge">Manufacturer</span>
                    </div>
                </nav>

                <div class="px-4 pb-4">
                    <!-- Production and Quality Row -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="production-card">
                                <h5 class="mb-2">Production Planning</h5>
                                <h3 class="mb-2">Active Lines: {{ $activeLines ?? 0 }}</h3>
                                <p class="mb-2">Daily output target: {{ $dailyOutput ?? 0 }} tons</p>
                                <div class="d-flex justify-content-between">
                                    <span>Efficiency: 94%</span>
                                    <span class="text-success">+2%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="quality-card">
                                <h5 class="mb-2">Quality Control</h5>
                                <h3 class="mb-2">Pass Rate: 98.5%</h3>
                                <p class="mb-2">Last 24 hours quality metrics</p>
                                <div class="d-flex justify-content-between">
                                    <span>Issues: {{ $qualityIssues ?? 0 }}</span>
                                    <span class="text-warning">-15%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Row -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-gear-wide-connected stats-icon"></i>
                                    <h5>Active Lines</h5>
                                    <h2>{{ $activeLines ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-arrow-up-circle stats-icon"></i>
                                    <h5>Daily Output</h5>
                                    <h2>{{ $dailyOutput ?? 0 }}<small style="font-size: 1rem; color: #718096;"> tons</small></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-exclamation-triangle stats-icon"></i>
                                    <h5>Quality Issues</h5>
                                    <h2>{{ $qualityIssues ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body position-relative">
                                    <i class="bi bi-box-seam stats-icon"></i>
                                    <h5>Raw Materials</h5>
                                    <h2>{{ $rawMaterials ?? 0 }}<small style="font-size: 1rem; color: #718096;"> tons</small></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card content-card">
                                <div class="card-header">
                                    <i class="bi bi-activity me-2"></i>Production Activity
                                </div>
                                <div class="card-body">
                                    @forelse ($recentActivity ?? [] as $activity)
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
                                        <a href="{{ route('manufacturer.production-lines.create') }}" class="btn-modern">
                                            <i class="bi bi-play-circle me-2"></i>Start Production
                                        </a>
                                        <a href="{{ route('manufacturer.quality-checks.create') }}" class="btn-modern">
                                            <i class="bi bi-shield-check me-2"></i>Quality Check
                                        </a>
                                        <a href="{{ route('manufacturer.raw-materials.create') }}" class="btn-modern">
                                            <i class="bi bi-boxes me-2"></i>Add Raw Material
                                        </a>
                                        <a href="{{ route('manufacturer.orders.create') }}" class="btn-secondary-modern">
                                            <i class="bi bi-plus-circle me-2"></i>Create Order
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
                                                                    <i class="bi bi-people" style="font-size: 2rem; opacity: 0.3; color: #667eea;"></i>
                                                                    <p class="text-muted mt-2 mb-0">No approved suppliers yet</p>
                                                                    <small class="text-muted">Approved suppliers will appear here</small>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title">Supplier Statistics</h6>
                                                    <div class="mb-3">
                                                        <small class="text-muted">Total Suppliers</small>
                                                        <h4 class="mb-0">{{ count($approvedSuppliers ?? []) }}</h4>
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">Average Rating</small>
                                                        <h4 class="mb-0">4.2/5.0</h4>
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">Active Contracts</small>
                                                        <h4 class="mb-0">12</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

<script>
function openChat(supplierId, supplierName) {
    // Implement chat functionality
    alert('Opening chat with ' + supplierName);
}

function viewSupplierDetails(supplierId) {
    // Implement supplier details view
    alert('Viewing details for supplier ID: ' + supplierId);
}

function requestQuote(supplierId) {
    // Implement quote request functionality
    alert('Requesting quote from supplier ID: ' + supplierId);
}
</script>
@endsection 