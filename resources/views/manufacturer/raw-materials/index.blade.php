@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Raw Materials Inventory</h5>
                    <a href="{{ route('manufacturer.raw-materials.create') }}" class="btn btn-primary">Add Raw Material</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Supplier</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Minimum Quantity</th>
                                    <th>Reorder Point</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($materials as $material)
                                <tr>
                                    <td>{{ $material->name }}</td>
                                    <td>{{ $material->supplier->name }}</td>
                                    <td>{{ $material->quantity }}</td>
                                    <td>{{ $material->unit }}</td>
                                    <td>{{ $material->minimum_quantity }}</td>
                                    <td>{{ $material->reorder_point }}</td>
                                    <td>
                                        @if($material->quantity <= $material->reorder_point)
                                            <span class="badge bg-danger">Reorder Needed</span>
                                        @elseif($material->quantity <= $material->minimum_quantity)
                                            <span class="badge bg-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">Sufficient</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('manufacturer.raw-materials.edit', $material) }}" class="btn btn-sm btn-info">Edit</a>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateStockModal{{ $material->id }}">
                                                Update Stock
                                            </button>
                                        </div>

                                        <!-- Update Stock Modal -->
                                        <div class="modal fade" id="updateStockModal{{ $material->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update Stock - {{ $material->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('manufacturer.raw-materials.update', $material) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Current Quantity</label>
                                                                <input type="text" class="form-control" value="{{ $material->quantity }} {{ $material->unit }}" readonly>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">New Quantity</label>
                                                                <input type="number" name="quantity" class="form-control" step="0.01" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Update Stock</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No raw materials found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 