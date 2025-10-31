@extends('customer::layouts.app')

@section('title', 'Browse Products')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-shop"></i> Browse Products</h2>
            <a href="{{ route('customer.cart.index') }}" class="btn btn-primary">
                <i class="bi bi-cart3"></i> Cart
                @if($cartCount > 0)
                    <span class="badge bg-danger">{{ $cartCount }}</span>
                @endif
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('customer.products.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="search" class="form-control"
                                       placeholder="Search products..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($products->count() > 0)
            <div class="row">
                @foreach($products as $product)
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div style="height: 200px; overflow: hidden; background: #f8f9fa;">
                                @if($product->getFirstMediaUrl('images'))
                                    <img src="{{ $product->getFirstMediaUrl('images') }}"
                                         class="card-img-top"
                                         style="width: 100%; height: 200px; object-fit: cover;"
                                         alt="{{ $product->product_name }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="bi bi-image" style="font-size: 3rem; color: #dee2e6;"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $product->product_name }}</h5>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-upc"></i> {{ $product->product_code }}
                                </p>

                                @if($product->category)
                                    <span class="badge bg-secondary mb-2" style="width: fit-content;">
                                        {{ $product->category->category_name }}
                                    </span>
                                @endif

                                @if($product->product_note)
                                    <p class="card-text small text-muted">
                                        {{ Str::limit($product->product_note, 80) }}
                                    </p>
                                @endif

                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="mb-0 text-primary">
                                            ${{ number_format($product->product_price / 100, 2) }}
                                        </h4>
                                        <span class="text-muted small">
                                            @if($product->product_quantity > 0)
                                                <i class="bi bi-check-circle text-success"></i> In Stock ({{ $product->product_quantity }})
                                            @else
                                                <i class="bi bi-x-circle text-danger"></i> Out of Stock
                                            @endif
                                        </span>
                                    </div>

                                    @if($product->product_quantity > 0)
                                        <form action="{{ route('customer.cart.add', $product) }}" method="POST">
                                            @csrf
                                            <div class="input-group mb-2">
                                                <input type="number" name="quantity" class="form-control"
                                                       value="1" min="1" max="{{ $product->product_quantity }}"
                                                       required>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <button class="btn btn-secondary btn-block" disabled>
                                            <i class="bi bi-x-circle"></i> Out of Stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-search" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3">No products found</h4>
                    <p class="text-muted">Try adjusting your search or filter criteria</p>
                    <a href="{{ route('customer.products.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-arrow-clockwise"></i> Reset Filters
                    </a>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Auto-submit form on category change
            document.querySelector('select[name="category"]').addEventListener('change', function() {
                this.form.submit();
            });
        </script>
    @endpush
@endsection