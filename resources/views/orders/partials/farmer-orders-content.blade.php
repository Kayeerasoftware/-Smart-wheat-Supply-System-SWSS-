{{-- Farmer Orders Content Partial --}}
<div class="glass-card p-8 rounded-2xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold font-space gradient-text">Farmer Orders</h1>
        <a href="{{ route('orders.create') }}" class="btn-primary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
            <i class="fas fa-plus mr-2"></i> New Order
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order #</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Products</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders ?? [] as $order)
                <tr class="table-row border-b border-gray-800">
                    <td class="px-6 py-4 font-medium text-white">{{ $order->order_number ?? 'ORD-' . $order->id }}</td>
                    <td class="px-6 py-4 text-white">{{ $order->customer->username ?? $order->customer->name ?? $order->customer->email ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-white">
                        @if($order->orderItems && $order->orderItems->count())
                            @foreach($order->orderItems as $item)
                                {{ $item->product->name ?? 'N/A' }}@if(!$loop->last), @endif
                            @endforeach
                        @else
                            <span class="text-red-400">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-white">${{ number_format($order->total_amount ?? 0, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-{{ strtolower($order->status ?? 'pending') }}">
                            <i class="fas fa-{{ $order->status == 'pending' ? 'clock' : ($order->status == 'shipped' ? 'truck' : ($order->status == 'delivered' ? 'check-double' : 'circle')) }}"></i>
                            {{ ucfirst($order->status ?? 'Pending') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-white">{{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-3">
                            <a href="{{ route('orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 transition-colors" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('orders.edit', $order) }}" class="text-green-400 hover:text-green-300 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                            <p class="text-lg font-medium">No orders found</p>
                            <p class="text-sm">Start by creating your first order</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($orders) && $orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-700">
        {{ $orders->links() }}
    </div>
    @endif
</div> 