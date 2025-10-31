@extends('layouts.app')

@section('title', 'Order Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.customer-orders.index') }}">Orders</a></li>
        <li class="breadcrumb-item active">Order #{{ $order->id }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <!-- Order Status Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order #{{ $order->id }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Current Status:</label>
                                <div>
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
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Order Date:</label>
                                <p>{{ $order->created_at->format('M d, Y - h:i A') }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Update Status:</label>
                                <form action="{{ route('admin.customer-orders.update-status', $order->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="input-group">
                                        <select name="status" class="form-select form-select-sm" required>
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                                            <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Ready</option>
                                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-check"></i> Update
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Code</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            @if($item->product && $item->product->getFirstMediaUrl('images'))
                                                <img src="{{ $item->product->getFirstMediaUrl('images', 'thumb') }}"
                                                     class="img-thumbnail me-2" style="width: 50px;">
                                            @endif
                                            {{ $item->product_name }}
                                        </td>
                                        <td>{{ $item->product_code }}</td>
                                        <td>{{ format_currency($item->unit_price) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td><strong>{{ format_currency($item->sub_total) }}</strong></td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                    <td><strong class="text-primary">{{ format_currency($order->total) }}</strong></td>
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
                            <h5 class="mb-0">Customer Notes</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $order->customerUser->name }}</strong></p>
                        <p class="mb-1">
                            <i class="bi bi-envelope"></i> {{ $order->customerUser->email }}
                        </p>
                        @if($order->customerUser->phone)
                            <p class="mb-0">
                                <i class="bi bi-telephone"></i> {{ $order->customerUser->phone }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="card mb-4">
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
                            <span>{{ format_currency($order->total) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5>Total:</h5>
                            <h5 class="text-primary">{{ format_currency($order->total) }}</h5>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
{{--                            <a href="{{ route('orders.print', $order->id) }}" class="btn btn-primary" target="_blank">--}}
{{--                                <i class="bi bi-printer"></i> Print Order--}}
{{--                            </a>--}}
                            <a href="{{ route('admin.customer-orders.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection