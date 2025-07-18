@extends('layouts.supplier')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold font-space mb-2 gradient-text">Supplier Analytics</h1>
    <p class="text-xl text-gray-300">Analytics dashboard for your business insights.</p>
</div>
<div class="glass-card p-6 mb-6">
    <h3 class="text-xl font-semibold text-white mb-4">Inventory Trend Graph</h3>
    @if(empty($inventoryTrendLabels))
        <div class="p-4 bg-yellow-500/10 rounded-lg text-yellow-400 text-center mb-4">
            No data available for the Inventory Trend chart at this time.
        </div>
    @else
        <canvas id="inventoryTrendChart" height="100"></canvas>
    @endif
</div>
<div class="glass-card p-6 mb-6">
    <h3 class="text-xl font-semibold text-white mb-4">Sales Performance</h3>
    @if(empty($salesPerformanceLabels))
        <div class="p-4 bg-yellow-500/10 rounded-lg text-yellow-400 text-center mb-4">
            No data available for the Sales Performance chart at this time.
        </div>
    @else
        <canvas id="salesPerformanceChart" height="100"></canvas>
    @endif
</div>
<div class="glass-card p-6 mb-6">
    <h3 class="text-xl font-semibold text-white mb-4">AI Demand Forecasting</h3>
    @php
        $hasForecastData = !empty($demandForecastData['historical_dates']) || !empty($demandForecastData['forecast_dates']);
    @endphp
    @if(!$hasForecastData)
        <div class="p-4 bg-yellow-500/10 rounded-lg text-yellow-400 text-center mb-4">
            No data available for the AI Demand Forecasting chart at this time.
        </div>
    @else
        <canvas id="demandForecastChart" height="100"></canvas>
    @endif
    <div class="mt-4 p-4 bg-blue-500/10 rounded-lg">
        <h4 class="text-lg font-semibold text-blue-400 mb-2">AI Recommendations</h4>
        <ul class="text-sm text-gray-300 space-y-1">
            @if(isset($demandForecastData['recommendations']))
                @foreach($demandForecastData['recommendations'] as $recommendation)
                    <li class="flex items-start">
                        <span class="text-blue-400 mr-2">•</span>
                        {{ $recommendation }}
                    </li>
                @endforeach
            @else
                <li class="flex items-start">
                    <span class="text-blue-400 mr-2">•</span>
                    Reduce wheat processing by 21.6%
                </li>
                <li class="flex items-start">
                    <span class="text-blue-400 mr-2">•</span>
                    Plan for 715 tons average monthly processing
                </li>
                <li class="flex items-start">
                    <span class="text-blue-400 mr-2">•</span>
                    Total expected demand: 4290 tons over next 6 months
                </li>
            @endif
        </ul>
    </div>
</div>
<div class="glass-card p-6 mb-6">
    <h3 class="text-xl font-semibold text-white mb-4">Supplier Demand Insights</h3>
    @if(!empty($supplierDemandInsights['chart_data']['labels']))
        <canvas id="supplierDemandChart" height="100"></canvas>
    @else
        <div class="p-4 bg-yellow-500/10 rounded-lg text-yellow-400 text-center mb-4">
            No data available for the Supplier Demand Insights chart at this time.
        </div>
    @endif
    <div class="mt-4 p-4 bg-green-500/10 rounded-lg">
        <h4 class="text-lg font-semibold text-green-400 mb-2">{{ $supplierDemandInsights['title'] ?? 'Supplier Insights' }}</h4>
        <p class="text-sm text-gray-300 mb-3">{{ $supplierDemandInsights['description'] ?? 'AI-powered insights for your business.' }}</p>
        
        @if(isset($supplierDemandInsights['type']) && $supplierDemandInsights['type'] === 'purchase_recommendations')
            <!-- ML-Based Purchase Recommendations -->
            <div class="space-y-3">
                @foreach($supplierDemandInsights['recommendations'] ?? [] as $recommendation)
                    <a href="{{ route('orders.create', ['product_id' => $recommendation['product_id']]) }}" class="block hover:bg-green-500/10 transition-all duration-200 rounded" title="Order {{ $recommendation['product'] }}">
                        <div class="bg-green-500/5 p-3 rounded border border-green-500/20">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 class="font-semibold text-green-400 underline hover:text-green-300">{{ $recommendation['product'] }}</h5>
                                    <p class="text-sm text-gray-300">{{ $recommendation['reason'] }}</p>
                                    <p class="text-xs text-gray-400">Price Range: {{ $recommendation['price_range'] }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-green-400">{{ number_format($recommendation['recommended_quantity']) }} kg</div>
                                    <div class="text-xs text-green-400 bg-green-500/20 px-2 py-1 rounded">{{ $recommendation['confidence'] }} Confidence</div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @elseif(isset($supplierDemandInsights['type']) && $supplierDemandInsights['type'] === 'market_products')
            <!-- Available Market Products -->
            <div class="mb-4 p-3 bg-blue-500/10 rounded-lg border border-blue-500/20">
                <p class="text-sm text-blue-300">
                    <i class="fas fa-info-circle mr-2"></i>
                    Click on any product card below to place an order directly from this farmer.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($supplierDemandInsights['products'] ?? [] as $product)
                    <div class="bg-green-500/5 p-3 rounded border border-green-500/20 hover:bg-green-500/10 hover:border-green-500/40 transition-all duration-300 cursor-pointer transform hover:scale-105" 
                         onclick="placeOrder({{ $product['farmer_id'] }}, {{ $product['product_id'] }}, '{{ $product['product_name'] }}', {{ $product['price_per_kg'] }})">
                        <div class="flex justify-between items-start mb-2">
                            <h5 class="font-semibold text-green-400">{{ $product['product_name'] }}</h5>
                            <div class="text-xs text-green-400 bg-green-500/20 px-2 py-1 rounded">
                                Grade {{ $product['quality_grade'] }}
                            </div>
                        </div>
                        <p class="text-sm text-gray-300 mb-1">
                            <i class="fas fa-user text-green-400 mr-1"></i>
                            {{ $product['farmer_name'] }}
                        </p>
                        <p class="text-sm text-gray-300 mb-2">
                            <i class="fas fa-map-marker-alt text-green-400 mr-1"></i>
                            {{ $product['location'] }}
                        </p>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-300">
                                    <i class="fas fa-box text-green-400 mr-1"></i>
                                    {{ number_format($product['quantity']) }} kg available
                                </p>
                                <p class="text-sm text-green-400 font-semibold">
                                    <i class="fas fa-dollar-sign text-green-400 mr-1"></i>
                                    ${{ number_format($product['price_per_kg'], 2) }}/kg
                                </p>
                            </div>
                            <div class="text-center">
                                <div class="text-xs text-green-400 bg-green-500/20 px-2 py-1 rounded mb-1">
                                    <i class="fas fa-shopping-cart mr-1"></i>
                                    Click to Order
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const inventoryTrendLabels = {!! json_encode($inventoryTrendLabels ?? []) !!};
    const inventoryTrendData = {!! json_encode($inventoryTrendData ?? []) !!};
    const ctx = document.getElementById('inventoryTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: inventoryTrendLabels,
            datasets: [{
                label: 'Inventory Count',
                data: inventoryTrendData,
                borderColor: 'rgba(102, 126, 234, 1)',
                backgroundColor: 'rgba(102, 126, 234, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: false }
            },
            scales: {
                x: { display: true, title: { display: true, text: 'Date' } },
                y: { display: true, title: { display: true, text: 'Inventory' } }
            }
        }
    });

    // Sales Performance Chart
    const salesPerformanceLabels = {!! json_encode($salesPerformanceLabels ?? []) !!};
    const salesPerformanceData = {!! json_encode($salesPerformanceData ?? []) !!};
    const salesPerformanceCtx = document.getElementById('salesPerformanceChart').getContext('2d');
    new Chart(salesPerformanceCtx, {
        type: 'bar',
        data: {
            labels: salesPerformanceLabels,
            datasets: [{
                label: 'Monthly Sales ($)',
                data: salesPerformanceData,
                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: false }
            },
            scales: {
                x: { 
                    display: true, 
                    title: { display: true, text: 'Month', color: '#fff' },
                    ticks: { color: '#fff' }
                },
                y: { 
                    display: true, 
                    title: { display: true, text: 'Sales ($)', color: '#fff' },
                    ticks: { color: '#fff' }
                }
            }
        }
    });

    // AI Demand Forecasting Chart
    // Chart will be added later
    
    // Simple AI Demand Forecasting Chart
    const demandForecastCtx = document.getElementById('demandForecastChart').getContext('2d');
    
    // Get forecast data from PHP
    const forecastData = {!! json_encode($demandForecastData ?? []) !!};
    
    // Prepare chart data
    const historicalDates = forecastData.historical_dates || [];
    const historicalValues = forecastData.historical_values || [];
    const forecastDates = forecastData.forecast_dates || [];
    const forecastValues = forecastData.forecast_values || [];
    
    // Combine all dates
    const allDates = [];
    for (let i = 0; i < historicalDates.length; i++) {
        allDates.push(historicalDates[i]);
    }
    for (let i = 0; i < forecastDates.length; i++) {
        allDates.push(forecastDates[i]);
    }
    
    // Create historical data array with null padding for forecast
    const historicalData = [];
    for (let i = 0; i < historicalValues.length; i++) {
        historicalData.push(historicalValues[i]);
    }
    for (let i = 0; i < forecastValues.length; i++) {
        historicalData.push(null);
    }
    
    // Create forecast data array with null padding for historical
    const forecastDataArray = [];
    for (let i = 0; i < historicalValues.length; i++) {
        forecastDataArray.push(null);
    }
    for (let i = 0; i < forecastValues.length; i++) {
        forecastDataArray.push(forecastValues[i]);
    }
    
    new Chart(demandForecastCtx, {
        type: 'line',
        data: {
            labels: allDates,
            datasets: [
                {
                    label: 'Historical Demand',
                    data: historicalData,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: false,
                    tension: 0.4
                },
                {
                    label: 'AI Forecast',
                    data: forecastDataArray,
                    borderColor: 'rgba(34, 197, 94, 1)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: false,
                    tension: 0.4,
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { 
                    display: true, 
                    position: 'top', 
                    labels: { color: '#fff' } 
                },
                title: { 
                    display: true, 
                    text: forecastData.title || 'AI Demand Forecast',
                    color: '#fff',
                    font: { size: 16 }
                }
            },
            scales: {
                x: { 
                    display: true, 
                    title: { display: true, text: 'Month', color: '#fff' },
                    ticks: { color: '#fff' }
                },
                y: { 
                    display: true, 
                    title: { display: true, text: 'Demand (tons)', color: '#fff' },
                    ticks: { color: '#fff' }
                }
            }
        }
    });
    
    // Supplier Demand Insights Chart
    const supplierDemandData = {!! json_encode($supplierDemandInsights ?? []) !!};
    const supplierDemandCtx = document.getElementById('supplierDemandChart').getContext('2d');
    
    if (supplierDemandData && supplierDemandData.chart_data) {
        const chartLabels = supplierDemandData.chart_data.labels || [];
        const chartQuantities = supplierDemandData.chart_data.quantities || [];
        const chartPrices = supplierDemandData.chart_data.prices || [];
        
        // Create bar chart for quantities
        new Chart(supplierDemandCtx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Quantity (kg)',
                        data: chartQuantities,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Price per kg ($)',
                        data: chartPrices,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'y1',
                        type: 'line'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { 
                        display: true, 
                        position: 'top', 
                        labels: { color: '#fff' } 
                    },
                    title: { 
                        display: true, 
                        text: supplierDemandData.title || 'Supplier Demand Insights',
                        color: '#fff',
                        font: { size: 16 }
                    }
                },
                scales: {
                    x: { 
                        display: true, 
                        title: { display: true, text: 'Products', color: '#fff' },
                        ticks: { color: '#fff' }
                    },
                    y: { 
                        display: true, 
                        type: 'linear',
                        position: 'left',
                        title: { display: true, text: 'Quantity (kg)', color: '#fff' },
                        ticks: { color: '#fff' }
                    },
                    y1: { 
                        display: true, 
                        type: 'linear',
                        position: 'right',
                        title: { display: true, text: 'Price ($/kg)', color: '#fff' },
                        ticks: { color: '#fff' },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    }

    // Function to handle order placement
    function placeOrder(farmerId, productId, productName, pricePerKg) {
        // Redirect directly to the quick create order page
        window.location.href = `/orders/quick-create/${farmerId}/${productId}`;
    }
</script>
@endpush 