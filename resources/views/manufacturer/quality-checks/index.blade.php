@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Quality Checks</h5>
                    <a href="{{ route('manufacturer.quality-checks.create') }}" class="btn btn-primary">New Quality Check</a>
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
                                    <th>Production Line</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Checked By</th>
                                    <th>Date</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($checks as $check)
                                <tr>
                                    <td>{{ $check->productionLine->name }}</td>
                                    <td>{{ $check->order->order_number }}</td>
                                    <td>
                                        <span class="badge bg-{{ $check->status === 'passed' ? 'success' : 'danger' }}">
                                            {{ ucfirst($check->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $check->checkedBy->name }}</td>
                                    <td>{{ $check->checked_at->format('M d, Y H:i') }}</td>
                                    <td>{{ Str::limit($check->notes, 50) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('manufacturer.quality-checks.edit', $check) }}" class="btn btn-sm btn-info">Edit</a>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewCheckModal{{ $check->id }}">
                                                View Details
                                            </button>
                                        </div>

                                        <!-- View Check Modal -->
                                        <div class="modal fade" id="viewCheckModal{{ $check->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Quality Check Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <dl class="row">
                                                            <dt class="col-sm-4">Production Line</dt>
                                                            <dd class="col-sm-8">{{ $check->productionLine->name }}</dd>

                                                            <dt class="col-sm-4">Order</dt>
                                                            <dd class="col-sm-8">{{ $check->order->order_number }}</dd>

                                                            <dt class="col-sm-4">Status</dt>
                                                            <dd class="col-sm-8">
                                                                <span class="badge bg-{{ $check->status === 'passed' ? 'success' : 'danger' }}">
                                                                    {{ ucfirst($check->status) }}
                                                                </span>
                                                            </dd>

                                                            <dt class="col-sm-4">Checked By</dt>
                                                            <dd class="col-sm-8">{{ $check->checkedBy->name }}</dd>

                                                            <dt class="col-sm-4">Date</dt>
                                                            <dd class="col-sm-8">{{ $check->checked_at->format('M d, Y H:i') }}</dd>

                                                            <dt class="col-sm-4">Notes</dt>
                                                            <dd class="col-sm-8">{{ $check->notes }}</dd>
                                                        </dl>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No quality checks found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $checks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 