@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Report Generator</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Delivery Report</h5>
                                    <p class="card-text">Generate and view delivery reports in your browser.</p>
                                    <a href="{{ route('reports.delivery') }}" class="btn btn-primary" target="_blank">
                                        <i class="bi bi-file-earmark-pdf me-2"></i>Generate Report
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Email Report</h5>
                                    <p class="card-text">Send report via email to any address.</p>
                                    <form action="{{ route('reports.email') }}" method="POST" class="d-inline">
                                        @csrf
                                        <div class="input-group mb-3">
                                            <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-envelope me-2"></i>Send
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Recent Reports</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Report Type</th>
                                        <th>Generated</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Delivery Report</td>
                                        <td>{{ now()->format('M d, Y H:i') }}</td>
                                        <td><span class="badge bg-success">Ready</span></td>
                                        <td>
                                            <a href="{{ route('reports.delivery') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 