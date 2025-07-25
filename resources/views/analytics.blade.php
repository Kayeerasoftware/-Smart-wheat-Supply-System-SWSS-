@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Analytics Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-600 text-white rounded-lg p-4 shadow">
            <div class="text-lg font-bold">Total Deliveries</div>
            <div class="text-3xl font-extrabold">128</div>
        </div>
        <div class="bg-green-600 text-white rounded-lg p-4 shadow">
            <div class="text-lg font-bold">On-Time %</div>
            <div class="text-3xl font-extrabold">93%</div>
        </div>
        <div class="bg-yellow-500 text-white rounded-lg p-4 shadow">
            <div class="text-lg font-bold">Delayed</div>
            <div class="text-3xl font-extrabold">9</div>
        </div>
        <div class="bg-purple-600 text-white rounded-lg p-4 shadow">
            <div class="text-lg font-bold">Top Deliverer</div>
            <div class="text-3xl font-extrabold">Jane Smith</div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg p-6 shadow">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Deliveries per Month</h2>
            <canvas id="deliveriesPerMonthChart" height="200"></canvas>
        </div>
        <div class="bg-white rounded-lg p-6 shadow">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Delivery Status Distribution</h2>
            <canvas id="deliveryStatusChart" height="200"></canvas>
        </div>
    </div>
    <div class="bg-white rounded-lg p-6 shadow mb-8">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Top Deliverers</h2>
        <canvas id="topDeliverersChart" height="120"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Deliveries per Month (Bar)
const deliveriesPerMonthCtx = document.getElementById('deliveriesPerMonthChart').getContext('2d');
new Chart(deliveriesPerMonthCtx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Deliveries',
            data: [10, 12, 15, 18, 20, 22, 19, 17, 21, 23, 25, 26],
            backgroundColor: 'rgba(102, 126, 234, 0.8)',
            borderRadius: 8,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
// Delivery Status Distribution (Doughnut)
const deliveryStatusCtx = document.getElementById('deliveryStatusChart').getContext('2d');
new Chart(deliveryStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['On Time', 'Delayed', 'In Transit'],
        datasets: [{
            data: [93, 9, 26],
            backgroundColor: [
                'rgba(34,197,94,0.8)',
                'rgba(234,179,8,0.8)',
                'rgba(102,126,234,0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom' } }
    }
});
// Top Deliverers (Horizontal Bar)
const topDeliverersCtx = document.getElementById('topDeliverersChart').getContext('2d');
new Chart(topDeliverersCtx, {
    type: 'bar',
    data: {
        labels: ['Jane Smith', 'John Doe', 'Mike Brown', 'Alice Green'],
        datasets: [{
            label: 'Deliveries',
            data: [34, 28, 22, 18],
            backgroundColor: [
                'rgba(168,85,247,0.8)',
                'rgba(59,130,246,0.8)',
                'rgba(16,185,129,0.8)',
                'rgba(251,191,36,0.8)'
            ],
            borderRadius: 8,
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true }
        }
    }
});
</script>
@endsection 