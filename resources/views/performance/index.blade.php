@extends('layouts.supplier')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold font-space mb-2 gradient-text">Supplier Performance</h1>
    <p class="text-xl text-gray-300">Performance dashboard for your business metrics.</p>
</div>
<div class="glass-card p-6 mb-6">
    <h3 class="text-xl font-semibold text-white mb-4">Performance Score</h3>
    @if($performanceScore !== null)
        <div class="text-center">
            <div class="text-6xl font-bold text-green-400 mb-4">{{ number_format($performanceScore, 1) }}</div>
            <div class="text-lg text-gray-300 mb-4">
                @if($performanceScore >= 90)
                    Excellent Performance
                @elseif($performanceScore >= 75)
                    Good Performance
                @elseif($performanceScore >= 60)
                    Average Performance
                @else
                    Needs Improvement
                @endif
            </div>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-blue-400">{{ $scoreFinancial !== null ? number_format($scoreFinancial, 0) : '--' }}</div>
                    <div class="text-xs text-gray-400">Financial</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-purple-400">{{ $scoreReputation !== null ? number_format($scoreReputation, 0) : '--' }}</div>
                    <div class="text-xs text-gray-400">Reputation</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-orange-400">{{ $scoreCompliance !== null ? number_format($scoreCompliance, 0) : '--' }}</div>
                    <div class="text-xs text-gray-400">Compliance</div>
                </div>
            </div>
        </div>
    @else
        <p class="text-gray-300">No performance score available yet.</p>
    @endif
</div>
<div class="glass-card p-6 mb-6">
    <h3 class="text-xl font-semibold text-white mb-4">Quality Metrics</h3>
    @if(isset($qualityMetrics))
        <div class="space-y-4">
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-300">Product Quality</span>
                    <span class="text-green-400 font-semibold">{{ $qualityMetrics['product_quality'] !== null ? $qualityMetrics['product_quality'] . '%' : '--' }}</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-300 bg-green-500" style="width: {{ $qualityMetrics['product_quality'] !== null ? $qualityMetrics['product_quality'] : 0 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-300">Delivery Accuracy</span>
                    <span class="text-green-400 font-semibold">{{ $qualityMetrics['delivery_accuracy'] !== null ? $qualityMetrics['delivery_accuracy'] . '%' : '--' }}</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-300 bg-green-500" style="width: {{ $qualityMetrics['delivery_accuracy'] !== null ? $qualityMetrics['delivery_accuracy'] : 0 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-300">Response Time</span>
                    <span class="text-yellow-400 font-semibold">{{ $qualityMetrics['response_time'] !== null ? $qualityMetrics['response_time'] . '%' : '--' }}</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-300 bg-yellow-400" style="width: {{ $qualityMetrics['response_time'] !== null ? $qualityMetrics['response_time'] : 0 }}%"></div>
                </div>
            </div>
        </div>
    @else
        <p class="text-gray-300">No quality metrics available yet.</p>
    @endif
</div>
<div class="glass-card p-6 mb-6">
    <h3 class="text-xl font-semibold text-white mb-4">Recent Achievements</h3>
    @if(isset($recentAchievements) && count($recentAchievements) > 0)
        <ul class="divide-y divide-gray-700">
            @foreach($recentAchievements as $achievement)
                <li class="py-3 flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <span class="font-semibold text-green-400">Order #{{ $achievement['order_id'] }}</span>
                        <span class="text-gray-300 ml-2">{{ $achievement['products'] }}</span>
                    </div>
                    <div class="text-sm text-gray-400 mt-1 md:mt-0">
                        <i class="fas fa-calendar-alt mr-1"></i>{{ $achievement['delivered_date'] ?? 'N/A' }}
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-300">No recent achievements yet.</p>
    @endif
</div>
@endsection 