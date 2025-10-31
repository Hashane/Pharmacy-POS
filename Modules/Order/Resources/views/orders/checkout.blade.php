@extends('customer::layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="py-4">
        <h2 class="mb-4"><i class="bi bi-check-circle"></i> Checkout</h2>

        <div class="row">
            <!-- Order Items -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Order Review</h4>
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
                                @foreach($cartItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product->getFirstMediaUrl('images'))
                                                    <img src="{{ $item->product->getFirstMediaUrl('images') }}"
                                                         class="me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $item->product->product_name }}</strong><br>
                                                    <small class="text-muted">{{ $item->product->product_code }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item->product->product_price / 100, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td><strong>${{ number_format(($item->product->product_price * $item->quantity) / 100, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Delivery/Pickup Instructions -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="mb-0">Additional Information</h4>
                    </div>
                    <div class="card-body">
                        <form id="checkoutForm" action="{{ route('customer.orders.place') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="notes">Order Notes (Optional)</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3"
                                          placeholder="Any special instructions or requirements..."></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items ({{ $cartItems->sum('quantity') }}):</span>
                            <span>${{ number_format($total / 100, 2) }}</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <h5>Total:</h5>
                            <h5 class="text-primary">${{ number_format($total / 100, 2) }}</h5>
                        </div>

                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle"></i>
                            <strong>No payment required.</strong><br>
                            Your order will be prepared and ready for pickup.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" form="checkoutForm" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Confirm Order
                            </button>
                            <a href="{{ route('customer.cart.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Cart
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Your Information</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ auth('customer')->user()->name }}</strong></p>
                        <p class="mb-1 text-muted small">{{ auth('customer')->user()->email }}</p>
                        @if(auth('customer')->user()->phone)
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-telephone"></i> {{ auth('customer')->user()->phone }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection