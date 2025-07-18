@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Production Insights</h1>
                    <p class="text-gray-600">Data-driven production planning based on customer segments</p>
                </div>
                <div>
                    <a href="{{ route('manufacturer.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            @if(isset($segments) && !empty($segments))
                <!-- Production Insights Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Demand</h5>
                                <h2>{{ count($segments['customers'] ?? []) * 100 }} tons</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Premium Demand</h5>
                                <h2>{{ count(array_filter($segments['customers'] ?? [], function($c) { return in_array($c['segment_name'] ?? '', ['Champions', 'Big Spenders']); })) * 50 }} tons</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Volume Demand</h5>
                                <h2>{{ count(array_filter($segments['customers'] ?? [], function($c) { return in_array($c['segment_name'] ?? '', ['Frequent Customers']); })) * 75 }} tons</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Standard Demand</h5>
                                <h2>{{ count(array_filter($segments['customers'] ?? [], function($c) { return !in_array($c['segment_name'] ?? '', ['Champions', 'Big Spenders', 'Frequent Customers']); })) * 25 }} tons</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Production Planning by Segment -->
                <div class="row">
                    @php
                        $segmentCounts = [];
                        foreach($segments['customers'] ?? [] as $customer) {
                            $segment = $customer['segment_name'] ?? 'Unknown';
                            $segmentCounts[$segment] = ($segmentCounts[$segment] ?? 0) + 1;
                        }
                    @endphp

                    @if(isset($segmentCounts['Champions']))
                    <div class="col-md-6 mb-4">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h4 class="mb-0">
                                    <i class="bi bi-star-fill me-2"></i>Champions Production Plan ({{ $segmentCounts['Champions'] }} customers)
                                </h4>
                            </div>
                            <div class="card-body">
                                <h6 class="text-success mb-3">Premium Production Strategy:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-gear text-success me-2"></i>
                                        <strong>Production Capacity:</strong> {{ $segmentCounts['Champions'] * 50 }} tons/month
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-shield-check text-success me-2"></i>
                                        <strong>Quality Level:</strong> Premium (99.5% pass rate)
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-clock text-success me-2"></i>
                                        <strong>Lead Time:</strong> 3-5 days (priority processing)
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-box text-success me-2"></i>
                                        <strong>Packaging:</strong> Premium packaging with certifications
                                    </li>
                                </ul>
                                <div class="mt-3">
                                    <span class="badge bg-success">Priority: Maximum</span>
                                    <span class="badge bg-danger">Quality: Premium</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($segmentCounts['Big Spenders']))
                    <div class="col-md-6 mb-4">
                        <div class="card border-purple">
                            <div class="card-header bg-purple text-white">
                                <h4 class="mb-0">
                                    <i class="bi bi-cash-stack me-2"></i>Big Spenders Production Plan ({{ $segmentCounts['Big Spenders'] }} customers)
                                </h4>
                            </div>
                            <div class="card-body">
                                <h6 class="text-purple mb-3">High-Value Production Strategy:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-gear text-purple me-2"></i>
                                        <strong>Production Capacity:</strong> {{ $segmentCounts['Big Spenders'] * 40 }} tons/month
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-shield-check text-purple me-2"></i>
                                        <strong>Quality Level:</strong> High (98.5% pass rate)
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-clock text-purple me-2"></i>
                                        <strong>Lead Time:</strong> 5-7 days (standard priority)
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-box text-purple me-2"></i>
                                        <strong>Packaging:</strong> High-quality packaging with branding
                                    </li>
                                </ul>
                                <div class="mt-3">
                                    <span class="badge bg-purple">Priority: High</span>
                                    <span class="badge bg-warning">Quality: High</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($segmentCounts['Frequent Customers']))
                    <div class="col-md-6 mb-4">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h4 class="mb-0">
                                    <i class="bi bi-arrow-repeat me-2"></i>Frequent Customers Production Plan ({{ $segmentCounts['Frequent Customers'] }} customers)
                                </h4>
                            </div>
                            <div class="card-body">
                                <h6 class="text-info mb-3">Volume Production Strategy:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-gear text-info me-2"></i>
                                        <strong>Production Capacity:</strong> {{ $segmentCounts['Frequent Customers'] * 75 }} tons/month
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-shield-check text-info me-2"></i>
                                        <strong>Quality Level:</strong> Standard (97% pass rate)
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-clock text-info me-2"></i>
                                        <strong>Lead Time:</strong> 7-10 days (efficiency focus)
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-box text-info me-2"></i>
                                        <strong>Packaging:</strong> Bulk packaging for cost efficiency
                                    </li>
                                </ul>
                                <div class="mt-3">
                                    <span class="badge bg-info">Priority: Medium</span>
                                    <span class="badge bg-primary">Quality: Standard</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($segmentCounts['Loyal Customers']))
                    <div class="col-md-6 mb-4">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">
                                    <i class="bi bi-heart-fill me-2"></i>Loyal Customers Production Plan ({{ $segmentCounts['Loyal Customers'] }} customers)
                                </h4>
                            </div>
                            <div class="card-body">
                                <h6 class="text-primary mb-3">Consistent Production Strategy:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-gear text-primary me-2"></i>
                                        <strong>Production Capacity:</strong> {{ $segmentCounts['Loyal Customers'] * 30 }} tons/month
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-shield-check text-primary me-2"></i>
                                        <strong>Quality Level:</strong> Consistent (97.5% pass rate)
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-clock text-primary me-2"></i>
                                        <strong>Lead Time:</strong> 5-8 days (reliable delivery)
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-box text-primary me-2"></i>
                                        <strong>Packaging:</strong> Standard packaging with loyalty branding
                                    </li>
                                </ul>
                                <div class="mt-3">
                                    <span class="badge bg-primary">Priority: Medium</span>
                                    <span class="badge bg-info">Quality: Consistent</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Production Optimization Recommendations -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="text-lg font-semibold">Production Optimization Recommendations</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h5 class="text-primary">Capacity Planning</h5>
                                        <ul>
                                            <li>Allocate 30% capacity to premium products (Champions/Big Spenders)</li>
                                            <li>Reserve 40% capacity for volume production (Frequent Customers)</li>
                                            <li>Use 30% capacity for standard production (other segments)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="text-warning">Quality Control</h5>
                                        <ul>
                                            <li>Implement tiered quality standards based on customer segments</li>
                                            <li>Increase quality checks for premium products</li>
                                            <li>Maintain consistent quality for loyal customers</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="text-success">Efficiency Improvements</h5>
                                        <ul>
                                            <li>Optimize production lines for high-volume segments</li>
                                            <li>Implement just-in-time production for premium segments</li>
                                            <li>Reduce lead times through process optimization</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Production Schedule -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="text-lg font-semibold">Recommended Production Schedule</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Segment</th>
                                                <th>Monthly Demand</th>
                                                <th>Production Priority</th>
                                                <th>Quality Level</th>
                                                <th>Lead Time</th>
                                                <th>Recommended Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($segmentCounts['Champions']))
                                            <tr class="table-success">
                                                <td>Champions</td>
                                                <td>{{ $segmentCounts['Champions'] * 50 }} tons</td>
                                                <td><span class="badge bg-danger">Maximum</span></td>
                                                <td>Premium (99.5%)</td>
                                                <td>3-5 days</td>
                                                <td>Dedicated production line</td>
                                            </tr>
                                            @endif
                                            @if(isset($segmentCounts['Big Spenders']))
                                            <tr class="table-purple">
                                                <td>Big Spenders</td>
                                                <td>{{ $segmentCounts['Big Spenders'] * 40 }} tons</td>
                                                <td><span class="badge bg-warning">High</span></td>
                                                <td>High (98.5%)</td>
                                                <td>5-7 days</td>
                                                <td>Priority scheduling</td>
                                            </tr>
                                            @endif
                                            @if(isset($segmentCounts['Frequent Customers']))
                                            <tr class="table-info">
                                                <td>Frequent Customers</td>
                                                <td>{{ $segmentCounts['Frequent Customers'] * 75 }} tons</td>
                                                <td><span class="badge bg-info">Medium</span></td>
                                                <td>Standard (97%)</td>
                                                <td>7-10 days</td>
                                                <td>Efficiency optimization</td>
                                            </tr>
                                            @endif
                                            @if(isset($segmentCounts['Loyal Customers']))
                                            <tr class="table-primary">
                                                <td>Loyal Customers</td>
                                                <td>{{ $segmentCounts['Loyal Customers'] * 30 }} tons</td>
                                                <td><span class="badge bg-primary">Medium</span></td>
                                                <td>Consistent (97.5%)</td>
                                                <td>5-8 days</td>
                                                <td>Reliable scheduling</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-gear" style="font-size: 4rem; color: #ccc;"></i>
                    <h3 class="mt-3 text-gray-600">No Production Data Available</h3>
                    <p class="text-gray-500">Run customer segmentation analysis first to get production insights.</p>
                    <a href="{{ route('manufacturer.customer-segments') }}" class="btn btn-primary btn-lg mt-3">
                        <i class="bi bi-graph-up me-2"></i>Run Segmentation Analysis
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 