@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Customer Segmentation for Production Planning</h1>
                    <p class="text-gray-600">Optimize production based on customer demand patterns</p>
                </div>
                <div>
                    <form action="{{ route('manufacturer.run-segmentation') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-play-circle me-2"></i>Run Segmentation Analysis
                        </button>
                    </form>
                    <a href="{{ route('manufacturer.dashboard') }}" class="btn btn-outline-secondary ms-2">
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
                <!-- Production Planning Summary -->
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
                                <h5 class="card-title">High-Value Customers</h5>
                                <h2>{{ count(array_filter($segments['customers'] ?? [], function($c) { return ($c['monetary'] ?? 0) > 1000; })) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Frequent Buyers</h5>
                                <h2>{{ count(array_filter($segments['customers'] ?? [], function($c) { return ($c['frequency'] ?? 0) > 10; })) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Production Priority</h5>
                                <h2>{{ count(array_filter($segments['customers'] ?? [], function($c) { return in_array($c['segment_name'] ?? '', ['Champions', 'Big Spenders']); })) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Segments Table -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold text-gray-800">Customer Segments for Production Planning</h2>
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
                                        <th>Purchase Pattern</th>
                                        <th>Production Priority</th>
                                        <th>Recommended Action</th>
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
                                            <td>
                                                @if(isset($customer['avg_days_between_purchases']) && $customer['avg_days_between_purchases'] > 0)
                                                    <small class="text-info">Every {{ round($customer['avg_days_between_purchases']) }} days</small><br>
                                                @endif
                                                @if(isset($customer['product_diversity']))
                                                    <small class="text-muted">{{ $customer['product_diversity'] }} products</small><br>
                                                @endif
                                                @if(isset($customer['spending_trend']))
                                                    @if($customer['spending_trend'] > 0.1)
                                                        <small class="text-success">↑ Increasing</small>
                                                    @elseif($customer['spending_trend'] < -0.1)
                                                        <small class="text-danger">↓ Decreasing</small>
                                                    @else
                                                        <small class="text-muted">→ Stable</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if(in_array($customer['segment_name'], ['Champions', 'Big Spenders']))
                                                    <span class="badge bg-danger">High</span>
                                                @elseif(in_array($customer['segment_name'], ['Loyal Customers', 'Frequent Customers']))
                                                    <span class="badge bg-warning">Medium</span>
                                                @else
                                                    <span class="badge bg-secondary">Standard</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($customer['segment_name'] == 'Champions')
                                                    <small class="text-success">Premium quality, priority production</small>
                                                @elseif($customer['segment_name'] == 'Big Spenders')
                                                    <small class="text-purple">High-value products, quality focus</small>
                                                @elseif($customer['segment_name'] == 'Frequent Customers')
                                                    <small class="text-info">Volume production, efficiency focus</small>
                                                @elseif($customer['segment_name'] == 'Loyal Customers')
                                                    <small class="text-primary">Consistent quality, reliable supply</small>
                                                @elseif($customer['segment_name'] == 'Recent Customers')
                                                    <small class="text-warning">Diverse product range, trial batches</small>
                                                @elseif($customer['segment_name'] == 'At Risk')
                                                    <small class="text-danger">Re-engagement focus</small>
                                                @else
                                                    <small class="text-muted">Standard production</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Production Insights -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="text-lg font-semibold">Production Priority Distribution</h3>
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
                                                <div class="progress-bar 
                                                    @if(in_array($segment, ['Champions', 'Big Spenders'])) bg-danger
                                                    @elseif(in_array($segment, ['Loyal Customers', 'Frequent Customers'])) bg-warning
                                                    @else bg-secondary
                                                    @endif" 
                                                    style="width: {{ ($count / count($segments['customers'])) * 100 }}%">
                                                </div>
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
                                <h3 class="text-lg font-semibold">Production Strategy Recommendations</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    @if(isset($segmentCounts['Champions']))
                                        <li class="mb-2">
                                            <i class="bi bi-star-fill text-warning me-2"></i>
                                            <strong>Champions ({{ $segmentCounts['Champions'] }}):</strong> Increase premium product capacity, enhance quality control
                                        </li>
                                    @endif
                                    @if(isset($segmentCounts['Big Spenders']))
                                        <li class="mb-2">
                                            <i class="bi bi-cash-stack text-success me-2"></i>
                                            <strong>Big Spenders ({{ $segmentCounts['Big Spenders'] }}):</strong> Focus on high-value products, maintain quality standards
                                        </li>
                                    @endif
                                    @if(isset($segmentCounts['Frequent Customers']))
                                        <li class="mb-2">
                                            <i class="bi bi-arrow-repeat text-info me-2"></i>
                                            <strong>Frequent Customers ({{ $segmentCounts['Frequent Customers'] }}):</strong> Optimize production lines for high volume
                                        </li>
                                    @endif
                                    @if(isset($segmentCounts['Loyal Customers']))
                                        <li class="mb-2">
                                            <i class="bi bi-heart-fill text-danger me-2"></i>
                                            <strong>Loyal Customers ({{ $segmentCounts['Loyal Customers'] }}):</strong> Ensure consistent quality and reliable supply
                                        </li>
                                    @endif
                                    @if(isset($segmentCounts['Recent Customers']))
                                        <li class="mb-2">
                                            <i class="bi bi-person-plus text-primary me-2"></i>
                                            <strong>Recent Customers ({{ $segmentCounts['Recent Customers'] }}):</strong> Maintain diverse product range for new markets
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-gear" style="font-size: 4rem; color: #ccc;"></i>
                    <h3 class="mt-3 text-gray-600">No Customer Segmentation Data</h3>
                    <p class="text-gray-500">Run the segmentation analysis to optimize your production planning based on customer demand patterns.</p>
                    <form action="{{ route('manufacturer.run-segmentation') }}" method="POST" class="mt-3">
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