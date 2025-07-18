@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Customer Segmentation Analysis</h1>
                    <p class="text-gray-600">Understand your customer base and optimize your retail strategy</p>
                </div>
                <div>
                    <form action="{{ route('retailer.run-segmentation') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-play-circle me-2"></i>Run Segmentation Analysis
                        </button>
                    </form>
                    <a href="{{ route('retailer.dashboard') }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(isset($segments) && !empty($segments))
                <!-- Segmentation Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Customers</h5>
                                <h2>{{ $segments['total_customers'] ?? count($segments['customers'] ?? []) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Segments</h5>
                                <h2>{{ count(array_unique(array_column($segments['customers'] ?? [], 'segment_name'))) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Avg. Value</h5>
                                <h2>${{ number_format(array_sum(array_column($segments['customers'] ?? [], 'monetary')) / max(1, count($segments['customers'] ?? [])), 0) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Avg. Frequency</h5>
                                <h2>{{ number_format(array_sum(array_column($segments['customers'] ?? [], 'frequency')) / max(1, count($segments['customers'] ?? [])), 1) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Segments Table -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold text-gray-800">Customer Segments</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>Segment</th>
                                        <th>Recency (Days)</th>
                                        <th>Frequency</th>
                                        <th>Total Spent ($)</th>
                                        <th>Avg Order Value</th>
                                        <th>Favorite Product</th>
                                        <th>Purchase Pattern</th>
                                        <th>Recommendations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($segments['customers'] ?? [] as $customerId => $customer)
                                        <tr>
                                            <td>{{ $customerId }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if($customer['segment_name'] == 'Champions') bg-success
                                                    @elseif($customer['segment_name'] == 'Loyal Customers') bg-primary
                                                    @elseif($customer['segment_name'] == 'Recent Customers') bg-warning
                                                    @elseif($customer['segment_name'] == 'Frequent Customers') bg-info
                                                    @elseif($customer['segment_name'] == 'Big Spenders') bg-purple
                                                    @elseif($customer['segment_name'] == 'At Risk') bg-danger
                                                    @else bg-secondary
                                                    @endif">
                                                    {{ $customer['segment_name'] }}
                                                </span>
                                            </td>
                                            <td>{{ $customer['recency'] }}</td>
                                            <td>{{ $customer['frequency'] }}</td>
                                            <td>${{ number_format($customer['total_spent'], 2) }}</td>
                                            <td>${{ number_format($customer['avg_order_value'], 2) }}</td>
                                            <td>{{ $customer['favorite_product'] ?? 'N/A' }}</td>
                                            <td>
                                                @if(isset($customer['avg_days_between_purchases']) && $customer['avg_days_between_purchases'] > 0)
                                                    <small class="text-info">Every {{ round($customer['avg_days_between_purchases']) }} days</small><br>
                                                @endif
                                                @if(isset($customer['product_diversity']))
                                                    <small class="text-muted">{{ $customer['product_diversity'] }} products</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($customer['segment_name'] == 'Champions')
                                                    <small class="text-success">VIP treatment, exclusive offers</small>
                                                @elseif($customer['segment_name'] == 'Loyal Customers')
                                                    <small class="text-primary">Loyalty rewards, retention campaigns</small>
                                                @elseif($customer['segment_name'] == 'Recent Customers')
                                                    <small class="text-warning">Welcome offers, onboarding</small>
                                                @elseif($customer['segment_name'] == 'Frequent Customers')
                                                    <small class="text-info">Volume discounts, subscription offers</small>
                                                @elseif($customer['segment_name'] == 'Big Spenders')
                                                    <small class="text-purple">Premium service, high-value products</small>
                                                @elseif($customer['segment_name'] == 'At Risk')
                                                    <small class="text-danger">Re-engagement campaigns</small>
                                                @else
                                                    <small class="text-muted">Promotional campaigns</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Segment Insights -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="text-lg font-semibold">Segment Distribution</h3>
                            </div>
                            <div class="card-body">
                                @php
                                    $segmentCounts = [];
                                    foreach($segments['customers'] ?? [] as $customer) {
                                        $segment = $customer['segment_name'] ?? 'Unknown';
                                        $segmentCounts[$segment] = ($segmentCounts[$segment] ?? 0) + 1;
                                    }
                                @endphp
                                @foreach($segmentCounts as $segment => $count)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ $segment }}</span>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 100px; height: 8px;">
                                                <div class="progress-bar" style="width: {{ ($count / count($segments['customers'])) * 100 }}%"></div>
                                            </div>
                                            <span class="badge bg-primary">{{ $count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="text-lg font-semibold">Retail Strategy Recommendations</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    @if(isset($segmentCounts['Champions']))
                                        <li class="mb-2">
                                            <i class="bi bi-star-fill text-warning me-2"></i>
                                            <strong>Champions ({{ $segmentCounts['Champions'] }}):</strong> Offer exclusive products and VIP services
                                        </li>
                                    @endif
                                    @if(isset($segmentCounts['Loyal Customers']))
                                        <li class="mb-2">
                                            <i class="bi bi-heart-fill text-danger me-2"></i>
                                            <strong>Loyal Customers ({{ $segmentCounts['Loyal Customers'] }}):</strong> Implement loyalty programs and retention strategies
                                        </li>
                                    @endif
                                    @if(isset($segmentCounts['Frequent Customers']))
                                        <li class="mb-2">
                                            <i class="bi bi-arrow-repeat text-info me-2"></i>
                                            <strong>Frequent Customers ({{ $segmentCounts['Frequent Customers'] }}):</strong> Provide volume discounts and subscription options
                                        </li>
                                    @endif
                                    @if(isset($segmentCounts['Big Spenders']))
                                        <li class="mb-2">
                                            <i class="bi bi-cash-stack text-success me-2"></i>
                                            <strong>Big Spenders ({{ $segmentCounts['Big Spenders'] }}):</strong> Focus on premium products and personalized service
                                        </li>
                                    @endif
                                    @if(isset($segmentCounts['Recent Customers']))
                                        <li class="mb-2">
                                            <i class="bi bi-person-plus text-primary me-2"></i>
                                            <strong>Recent Customers ({{ $segmentCounts['Recent Customers'] }}):</strong> Engage with welcome offers and onboarding
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 4rem; color: #ccc;"></i>
                    <h3 class="mt-3 text-gray-600">No Customer Segmentation Data</h3>
                    <p class="text-gray-500">Run the segmentation analysis to understand your customer base and optimize your retail strategy.</p>
                    <form action="{{ route('retailer.run-segmentation') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-play-circle me-2"></i>Start Segmentation Analysis
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 