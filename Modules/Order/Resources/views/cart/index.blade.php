@extends('customer::layouts.app')

@section('title', 'Shopping Cart')

@section('content')
    <div class="py-4">
        <h2 class="mb-4"><i class="bi bi-cart3"></i> Shopping Cart</h2>

        @if($cartItems->count() > 0)
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th></th>
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
                                            <td>
                                                <form action="{{ route('customer.cart.update', $item) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="input-group" style="width: 120px;">
                                                        <input type="number" name="quantity" class="form-control form-control-sm"
                                                               value="{{ $item->quantity }}" min="1" max="{{ $item->product->product_quantity }}"
                                                               onchange="this.form.submit()">
                                                    </div>
                                                </form>
                                            </td>
                                            <td><strong>${{ number_format($item->sub_total / 100, 2) }}</strong></td>
                                            <td>
                                                <form action="{{ route('customer.cart.remove', $item) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Remove this item?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <form action="{{ route('customer.cart.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"
                                            onclick="return confirm('Clear entire cart?')">
                                        <i class="bi bi-trash"></i> Clear Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <strong>${{ number_format($total / 100, 2) }}</strong>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-3">
                                <h5>Total:</h5>
                                <h5 class="text-primary">${{ number_format($total / 100, 2) }}</h5>
                            </div>

                            <div class="alert alert-info small">
                                <i class="bi bi-info-circle"></i> No payment required. Order will be prepared for pickup.
                            </div>

                            <div class="d-grid">
                                <a href="{{ route('customer.orders.checkout') }}" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle"></i> Proceed to Checkout
                                </a>
                            </div>

                            <div class="d-grid mt-2">
                                <a href="{{ route('customer.products.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3">Your cart is empty</h4>
                    <p class="text-muted">Add some products to get started!</p>
                    <a href="{{ route('customer.products.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-shop"></i> Browse Products
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection