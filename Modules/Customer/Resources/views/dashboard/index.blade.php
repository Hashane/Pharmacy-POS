@extends('customer::layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                        <h5 class="card-title text-muted">Loyalty Balance</h5>
                        <h2 class="display-5 my-3 text-primary">
                            {{ auth('customer')->user()->loyalty_balance }}
                            <span class="fs-5">pts</span>
                        </h2>
                        <p class="text-muted mb-0">Points earned from your completed orders</p>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Orders</h6>
                                <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                            </div>
                            <div class="text-primary" style="font-size: 2rem;">
                                <i class="bi bi-bag-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Pending Orders</h6>
                                <h3 class="mb-0">{{ $stats['pending_orders'] }}</h3>
                            </div>
                            <div class="text-warning" style="font-size: 2rem;">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Ready for Pickup</h6>
                                <h3 class="mb-0">{{ $stats['ready_orders'] }}</h3>
                            </div>
                            <div class="text-success" style="font-size: 2rem;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Prescriptions</h6>
                                <h3 class="mb-0">{{ $stats['total_prescriptions'] }}</h3>
                            </div>
                            <div class="text-info" style="font-size: 2rem;">
                                <i class="bi bi-file-medical"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bag-check"></i> Recent Orders</h5>
                <a href="{{ route('customer.orders.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td><strong>{{ $order->reference }}</strong></td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>${{ number_format($order->total_amount / 100, 2) }}</td>
                                    <td>
                                <span class="badge bg-{{ $order->status_badge }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">No orders yet. Start shopping!</p>
                @endif
            </div>
        </div>

        <!-- Recent Prescriptions -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-medical"></i> Recent Prescriptions</h5>
                <a href="{{ route('customer.prescriptions.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if($prescriptions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Files</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($prescriptions as $prescription)
                                <tr>
                                    <td><strong>{{ $prescription->reference }}</strong></td>
                                    <td>{{ $prescription->created_at->format('M d, Y') }}</td>
                                    <td>{{ $prescription->files->count() }} file(s)</td>
                                    <td>
                                <span class="badge bg-{{ $prescription->status === 'approved' ? 'success' : ($prescription->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($prescription->status) }}
                                </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.prescriptions.show', $prescription) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">No prescriptions yet.</p>
                @endif
            </div>
        </div>
    </div>
@endsection