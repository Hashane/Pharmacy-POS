<?php

namespace Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Category;

class CustomerProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('prescription_required', false) // Only show OTC products
            ->with(['category', 'media']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%')
                    ->orWhere('product_code', 'like', '%' . $request->search . '%');
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(12);
        $categories = Category::all();
        $cartCount = Auth::guard('customer')->check()
            ? Auth::guard('customer')->user()->cartItems()->sum('quantity')
            : 0;

        return view('customer::products.index', compact('products', 'categories','cartCount'));
    }

    public function show(Product $product)
    {
        // Only show OTC products
        if ($product->prescription_required) {
            abort(404);
        }

        $product->load(['category', 'media']);

        return view('products::show', compact('product'));
    }
}