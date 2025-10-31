@extends('customer::layouts.app')

@section('title', 'My Orders')

@section('content')
    <div class="py-4">
        <h2 class="mb-4"><i class="bi bi-bag-check"></i> My Orders</h2>

        @if($orders->count() > 0)
            <div class="row">
                @foreach($orders as $order)
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <strong>Order #{{ $order->id }}</strong><br>
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y - h:i A') }}</small>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="badge
                                            @if($order->status == 'pending') bg-warning
                                            @elseif($order->status == 'preparing') bg-info
                                            @elseif($order->status == 'ready') bg-success
                                            @elseif($order->status == 'completed') bg-primary
                                            @elseif($order->status == 'cancelled') bg-danger
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <strong class="text-primary">${{ number_format($order->total / 100, 2) }}</strong>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="mb-2">Items ({{ $order->items->count() }}):</h6>
                                        <ul class="list-unstyled mb-0">
                                            @foreach($order->items->take(3) as $item)
                                                <li class="mb-1">
                                                    <i class="bi bi-box-seam"></i>
                                                    {{ $item->product_name }}
                                                    <span class="text-muted">x{{ $item->quantity }}</span>
                                                </li>
                                            @endforeach
                                            @if($order->items->count() > 3)
                                                <li class="text-muted">
                                                    <small>+ {{ $order->items->count() - 3 }} more items</small>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        @if($order->notes)
                                            <h6 class="mb-2">Notes:</h6>
                                            <p class="text-muted small mb-0">{{ Str::limit($order->notes, 80) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-bag-x" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3">No orders yet</h4>
                    <p class="text-muted">Start shopping to place your first order!</p>
                    <a href="{{ route('customer.products.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-shop"></i> Browse Products
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection