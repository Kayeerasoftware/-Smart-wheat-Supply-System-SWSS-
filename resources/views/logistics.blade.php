@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Logistics Management</h1>
    <div class="mb-4 flex gap-2">
        <button class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">Add New Route</button>
        <button class="btn btn-secondary bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">Optimize Routes</button>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold py-3">Active Routes</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2">Route ID</th>
                            <th class="px-4 py-2">Origin</th>
                            <th class="px-4 py-2">Destination</th>
                            <th class="px-4 py-2">Deliverer</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">ETA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2">R001</td>
                            <td class="px-4 py-2">Warehouse A</td>
                            <td class="px-4 py-2">Client X</td>
                            <td class="px-4 py-2">John Doe</td>
                            <td class="px-4 py-2"><span class="badge bg-success">In Transit</span></td>
                            <td class="px-4 py-2">1h 20m</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2">R002</td>
                            <td class="px-4 py-2">Warehouse B</td>
                            <td class="px-4 py-2">Client Y</td>
                            <td class="px-4 py-2">Jane Smith</td>
                            <td class="px-4 py-2"><span class="badge bg-warning">Delayed</span></td>
                            <td class="px-4 py-2">2h 10m</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2">R003</td>
                            <td class="px-4 py-2">Warehouse C</td>
                            <td class="px-4 py-2">Client Z</td>
                            <td class="px-4 py-2">Mike Brown</td>
                            <td class="px-4 py-2"><span class="badge bg-secondary">Delivered</span></td>
                            <td class="px-4 py-2">--</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 