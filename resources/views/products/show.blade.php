<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $product->name }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('products.edit', $product) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Product
                </a>
                <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Products
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Product Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Product Images -->
                        <div class="lg:col-span-1">
                            @if($product->images && count($product->images) > 0)
                                <img src="{{ Storage::url($product->images[0]) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-64 object-cover rounded-lg">
                            @else
                                <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Product Details -->
                        <div class="lg:col-span-2">
                            <div class="space-y-6">
                                <!-- Basic Info -->
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h3>
                                    <p class="text-gray-600 mt-1">SKU: {{ $product->sku }}</p>
                                    @if($product->brand)
                                        <p class="text-gray-600">Brand: {{ $product->brand }}</p>
                                    @endif
                                </div>

                                <!-- Status Badge -->
                                <div>
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                        {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                    <div class="mt-2 text-sm text-gray-600">
                                        @if($product->is_raw_material) 
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                Raw Material
                                            </span>
                                        @endif
                                        @if($product->is_finished_good) 
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Finished Good
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Description -->
                                @if($product->description)
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900 mb-2">Description</h4>
                                        <p class="text-gray-600">{{ $product->description }}</p>
                                    </div>
                                @endif

                                <!-- Category -->
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-2">Category</h4>
                                    <p class="text-gray-600">{{ $product->category->name ?? 'N/A' }}</p>
                                </div>

                                <!-- Pricing -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900 mb-2">Pricing</h4>
                                        <p class="text-2xl font-bold text-green-600">${{ number_format($product->unit_price, 2) }}</p>
                                        <p class="text-sm text-gray-500">Cost: ${{ number_format($product->cost_price, 2) }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900 mb-2">Unit</h4>
                                        <p class="text-gray-600">{{ $product->unit_of_measure }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $inventorySummary['total_on_hand'] ?? 0 }}</div>
                            <div class="text-sm text-blue-600">Total On Hand</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $inventorySummary['total_available'] ?? 0 }}</div>
                            <div class="text-sm text-green-600">Available</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ $inventorySummary['total_reserved'] ?? 0 }}</div>
                            <div class="text-sm text-yellow-600">Reserved</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">${{ number_format($inventorySummary['total_value'] ?? 0, 2) }}</div>
                            <div class="text-sm text-purple-600">Total Value</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory by Warehouse -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory by Warehouse</h3>
                    @if($product->inventories->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">On Hand</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserved</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">On Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($product->inventories as $inventory)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $inventory->warehouse->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $inventory->quantity_on_hand }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $inventory->quantity_available }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $inventory->quantity_reserved }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $inventory->quantity_on_order }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${{ number_format($inventory->quantity_on_hand * $product->cost_price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($inventory->quantity_available <= $product->reorder_point)
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        Low Stock
                                                    </span>
                                                @elseif($inventory->quantity_available == 0)
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        Out of Stock
                                                    </span>
                                                @else
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        In Stock
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No inventory records found.</p>
                    @endif
                </div>
            </div>

            <!-- Low Stock Alerts -->
            @if(isset($inventorySummary['low_stock_alerts']) && count($inventorySummary['low_stock_alerts']) > 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Low Stock Alerts</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($inventorySummary['low_stock_alerts'] as $alert)
                                        <li>{{ $alert['warehouse'] }}: {{ $alert['available'] }} available (Reorder point: {{ $alert['reorder_point'] }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Product Specifications -->
            @if($product->specifications && count($product->specifications) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Specifications</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($product->specifications as $key => $value)
                                <div class="border-b border-gray-200 pb-2">
                                    <dt class="text-sm font-medium text-gray-500">{{ ucfirst($key) }}</dt>
                                    <dd class="text-sm text-gray-900 mt-1">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 