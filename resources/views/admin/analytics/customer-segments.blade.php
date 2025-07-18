@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Customer Segmentation Analysis</h1>
        <form method="POST" action="{{ route('admin.analytics.run-segmentation') }}" class="inline">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Run Segmentation Analysis
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(!empty($segments))
        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Total Customers</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $segments['summary']['total_customers'] ?? 0 }}</p>
            </div>
            
            @foreach($segments['summary']['segment_distribution'] ?? [] as $segment => $count)
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">{{ $segment }}</h3>
                <p class="text-3xl font-bold text-green-600">{{ $count }}</p>
            </div>
            @endforeach
        </div>

        <!-- Customer Segments Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Customer Segments</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Segment
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Recency (days)
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Frequency
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Monetary ($)
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($segments['customers'] ?? [] as $customerId => $customer)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $customerId }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($customer['segment_name'] == 'Champions') bg-green-100 text-green-800
                                    @elseif($customer['segment_name'] == 'Loyal Customers') bg-blue-100 text-blue-800
                                    @elseif($customer['segment_name'] == 'Recent Customers') bg-yellow-100 text-yellow-800
                                    @elseif($customer['segment_name'] == 'Frequent Customers') bg-purple-100 text-purple-800
                                    @elseif($customer['segment_name'] == 'Big Spenders') bg-indigo-100 text-indigo-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $customer['segment_name'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($customer['recency']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($customer['frequency']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($customer['monetary'], 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Segment Recommendations</h2>
            </div>
            <div class="p-6">
                @foreach($segments['recommendations'] ?? [] as $segment => $recommendations)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">{{ $segment }}</h3>
                    <ul class="list-disc list-inside space-y-2">
                        @foreach($recommendations as $recommendation)
                        <li class="text-gray-600">{{ $recommendation }}</li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            No customer segmentation data available. Click "Run Segmentation Analysis" to generate the first analysis.
        </div>
    @endif
</div>
@endsection 