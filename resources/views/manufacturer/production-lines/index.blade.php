@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Production Lines</h5>
                    <a href="{{ route('manufacturer.production-lines.create') }}" class="btn btn-primary">Add New Line</a>
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
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Current Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lines as $line)
                                <tr>
                                    <td>{{ $line->name }}</td>
                                    <td>{{ $line->capacity }}</td>
                                    <td>
                                        <span class="badge bg-{{ $line->status === 'active' ? 'success' : ($line->status === 'maintenance' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($line->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($line->currentOrder)
                                            {{ $line->currentOrder->order_number }}
                                        @else
                                            <span class="text-muted">No active order</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('manufacturer.production-lines.edit', $line) }}" class="btn btn-sm btn-info">Edit</a>
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#qualityCheckModal{{ $line->id }}">
                                                Quality Check
                                            </button>
                                            @if($line->status === 'active')
                                                <form action="{{ route('manufacturer.production-lines.update', $line) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="maintenance">
                                                    <button type="submit" class="btn btn-sm btn-warning">Maintenance</button>
                                                </form>
                                            @else
                                                <form action="{{ route('manufacturer.production-lines.update', $line) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="btn btn-sm btn-success">Activate</button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- Quality Check Modal -->
                                        <div class="modal fade" id="qualityCheckModal{{ $line->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Quality Check - {{ $line->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('manufacturer.quality-checks.store') }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <input type="hidden" name="production_line_id" value="{{ $line->id }}">
                                                            <input type="hidden" name="order_id" value="{{ $line->currentOrder->id ?? '' }}">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select name="status" class="form-select" required>
                                                                    <option value="passed">Passed</option>
                                                                    <option value="failed">Failed</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Notes</label>
                                                                <textarea name="notes" class="form-control" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Submit Check</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No production lines found</td>
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