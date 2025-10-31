@extends('customer::layouts.app')

@section('title', 'Order Details')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-receipt"></i> Order #{{ $order->id }}</h2>
            <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>

        <div class="row">
            <!-- Order Details -->
            <div class="col-lg-8">
                <!-- Order Status -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Order Status</h5>
                                <span class="badge 
                                    @if($order->status == 'pending') bg-warning
                                    @elseif($order->status == 'preparing') bg-info
                                    @elseif($order->status == 'ready') bg-success
                                    @elseif($order->status == 'completed') bg-primary
                                    @elseif($order->status == 'cancelled') bg-danger
                                    @endif" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <h5>Order Date</h5>
                                <p class="mb-0">{{ $order->created_at->format('F d, Y - h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Order Items</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->getFirstMediaUrl('images'))
                                                    <img src="{{ $item->product->getFirstMediaUrl('images') }}"
                                                         class="me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $item->product_name }}</strong><br>
                                                    <small class="text-muted">{{ $item->product_code }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item->unit_price / 100, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td><strong>${{ number_format($item->sub_total / 100, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong class="text-primary">${{ number_format($order->total / 100, 2) }}</strong></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                @if($order->notes)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Notes</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Customer Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $order->customerUser->name }}</strong></p>
                        <p class="mb-1 text-muted">
                            <i class="bi bi-envelope"></i> {{ $order->customerUser->email }}
                        </p>
                        @if($order->customerUser->phone)
                            <p class="mb-0 text-muted">
                                <i class="bi bi-telephone"></i> {{ $order->customerUser->phone }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items:</span>
                            <span>{{ $order->items->sum('quantity') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>${{ number_format($order->total / 100, 2) }}</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <h5>Total:</h5>
                            <h5 class="text-primary">${{ number_format($order->total / 100, 2) }}</h5>
                        </div>

                        <div class="alert alert-info small mt-3 mb-0">
                            <i class="bi bi-info-circle"></i> No payment required. Order will be prepared for pickup.
                        </div>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item {{ $order->status == 'pending' || $order->status == 'preparing' || $order->status == 'ready' || $order->status == 'completed' ? 'active' : '' }}">
                                <i class="bi bi-check-circle"></i>
                                <span>Order Placed</span>
                            </div>
                            <div class="timeline-item {{ $order->status == 'preparing' || $order->status == 'ready' || $order->status == 'completed' ? 'active' : '' }}">
                                <i class="bi bi-hourglass-split"></i>
                                <span>Preparing</span>
                            </div>
                            <div class="timeline-item {{ $order->status == 'ready' || $order->status == 'completed' ? 'active' : '' }}">
                                <i class="bi bi-box-seam"></i>
                                <span>Ready for Pickup</span>
                            </div>
                            <div class="timeline-item {{ $order->status == 'completed' ? 'active' : '' }}">
                                <i class="bi bi-check-all"></i>
                                <span>Completed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .timeline {
                position: relative;
            }
            .timeline-item {
                padding: 10px 0;
                padding-left: 35px;
                position: relative;
                color: #999;
            }
            .timeline-item i {
                position: absolute;
                left: 0;
                top: 10px;
                font-size: 1.2rem;
            }
            .timeline-item.active {
                color: #28a745;
                font-weight: 500;
            }
            .timeline-item:not(:last-child)::before {
                content: '';
                position: absolute;
                left: 8px;
                top: 30px;
                height: calc(100% - 10px);
                width: 2px;
                background: #ddd;
            }
            .timeline-item.active:not(:last-child)::before {
                background: #28a745;
            }
        </style>
    @endpush
@endsection