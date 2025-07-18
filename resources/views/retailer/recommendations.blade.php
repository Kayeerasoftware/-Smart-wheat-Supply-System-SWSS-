@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Personalized Recommendations</h1>
                    <p class="text-gray-600">AI-powered recommendations to boost your retail performance</p>
                </div>
                <div>
                    <a href="{{ route('retailer.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            @if(isset($segments) && !empty($segments))
                <!-- Recommendations Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Recommendations</h5>
                                <h2>{{ count($segments['customers'] ?? []) * 3 }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">High-Value Opportunities</h5>
                                <h2>{{ count(array_filter($segments['customers'] ?? [], function($c) { return in_array($c['segment_name'] ?? '', ['Champions', 'Big Spenders']); })) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Retention Focus</h5>
                                <h2>{{ count(array_filter($segments['customers'] ?? [], function($c) { return in_array($c['segment_name'] ?? '', ['Loyal Customers', 'Frequent Customers']); })) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Growth Potential</h5>
                                <h2>{{ count(array_filter($segments['customers'] ?? [], function($c) { return in_array($c['segment_name'] ?? '', ['Recent Customers']); })) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Segment-Based Recommendations -->
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
                                    <i class="bi bi-star-fill me-2"></i>Champions ({{ $segmentCounts['Champions'] }} customers)
                                </h4>
                            </div>
                            <div class="card-body">
                                <h6 class="text-success mb-3">VIP Strategy Recommendations:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        Offer exclusive premium products and early access
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        Provide personalized customer service and dedicated support
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        Create VIP loyalty programs with higher rewards
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        Send personalized recommendations and exclusive offers
                                    </li>
                                </ul>
                                <div class="mt-3">
                                    <span class="badge bg-success">Priority: High</span>
                                    <span class="badge bg-primary">Revenue Impact: Maximum</span>
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
                                    <i class="bi bi-cash-stack me-2"></i>Big Spenders ({{ $segmentCounts['Big Spenders'] }} customers)
                                </h4>
                            </div>
                            <div class="card-body">
                                <h6 class="text-purple mb-3">High-Value Strategy Recommendations:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-purple me-2"></i>
                                        Focus on premium and high-margin products
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-purple me-2"></i>
                                        Offer bulk purchase discounts and package deals
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-purple me-2"></i>
                                        Provide premium customer service and support
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-purple me-2"></i>
                                        Create exclusive high-value product bundles
                                    </li>
                                </ul>
                                <div class="mt-3">
                                    <span class="badge bg-purple">Priority: High</span>
                                    <span class="badge bg-success">Revenue Impact: High</span>
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
                                    <i class="bi bi-arrow-repeat me-2"></i>Frequent Customers ({{ $segmentCounts['Frequent Customers'] }} customers)
                                </h4>
                            </div>
                            <div class="card-body">
                                <h6 class="text-info mb-3">Volume Strategy Recommendations:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-info me-2"></i>
                                        Offer volume discounts and subscription services
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-info me-2"></i>
                                        Create loyalty programs with tier-based rewards
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-info me-2"></i>
                                        Provide convenient reordering and auto-refill options
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-info me-2"></i>
                                        Send regular updates and new product notifications
                                    </li>
                                </ul>
                                <div class="mt-3">
                                    <span class="badge bg-info">Priority: Medium</span>
                                    <span class="badge bg-warning">Revenue Impact: Medium</span>
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
                                    <i class="bi bi-heart-fill me-2"></i>Loyal Customers ({{ $segmentCounts['Loyal Customers'] }} customers)
                                </h4>
                            </div>
                            <div class="card-body">
                                <h6 class="text-primary mb-3">Retention Strategy Recommendations:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-primary me-2"></i>
                                        Implement comprehensive loyalty programs
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-primary me-2"></i>
                                        Provide consistent quality and reliable service
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-primary me-2"></i>
                                        Offer exclusive member benefits and early access
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-primary me-2"></i>
                                        Create community engagement and feedback programs
                                    </li>
                                </ul>
                                <div class="mt-3">
                                    <span class="badge bg-primary">Priority: Medium</span>
                                    <span class="badge bg-info">Revenue Impact: Stable</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($segmentCounts['Recent Customers']))
                    <div class="col-md-6 mb-4">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-white">
                                <h4 class="mb-0">
                                    <i class="bi bi-person-plus me-2"></i>Recent Customers ({{ $segmentCounts['Recent Customers'] }} customers)
                                </h4>
                            </div>
                            <div class="card-body">
                                <h6 class="text-warning mb-3">Engagement Strategy Recommendations:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-warning me-2"></i>
                                        Send welcome offers and onboarding materials
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-warning me-2"></i>
                                        Provide educational content and product guides
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-warning me-2"></i>
                                        Offer trial products and sample packages
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-warning me-2"></i>
                                        Create engagement campaigns and feedback requests
                                    </li>
                                </ul>
                                <div class="mt-3">
                                    <span class="badge bg-warning">Priority: Medium</span>
                                    <span class="badge bg-secondary">Revenue Impact: Growth</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Action Plan -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="text-lg font-semibold">Recommended Action Plan</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h5 class="text-primary">Immediate Actions (This Week)</h5>
                                        <ul>
                                            <li>Implement VIP program for Champions</li>
                                            <li>Create premium product bundles for Big Spenders</li>
                                            <li>Set up volume discounts for Frequent Customers</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="text-warning">Short-term Actions (Next Month)</h5>
                                        <ul>
                                            <li>Launch loyalty program for all segments</li>
                                            <li>Develop personalized marketing campaigns</li>
                                            <li>Create customer feedback system</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="text-success">Long-term Actions (Next Quarter)</h5>
                                        <ul>
                                            <li>Implement AI-powered recommendations</li>
                                            <li>Develop mobile app with personalized features</li>
                                            <li>Create customer success team</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-lightbulb" style="font-size: 4rem; color: #ccc;"></i>
                    <h3 class="mt-3 text-gray-600">No Customer Data Available</h3>
                    <p class="text-gray-500">Run customer segmentation analysis first to get personalized recommendations.</p>
                    <a href="{{ route('retailer.customer-segments') }}" class="btn btn-primary btn-lg mt-3">
                        <i class="bi bi-graph-up me-2"></i>Run Segmentation Analysis
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 