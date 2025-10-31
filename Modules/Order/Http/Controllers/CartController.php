<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\Entities\CartItem;
use Modules\Product\Entities\Product;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::where('customer_user_id', auth('customer')->id())
            ->with('product.media')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->product->product_price * $item->quantity;
        });

        return view('order::cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        // Ensure product is OTC
        if ($product->prescription_required) {
            return back()->with('error', 'This product requires a prescription.');
        }

        // Check stock
        if ($product->product_quantity < 1) {
            return back()->with('error', 'Product is out of stock.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->product_quantity,
        ]);

        $cartItem = CartItem::firstOrNew([
            'customer_user_id' => auth('customer')->id(),
            'product_id' => $product->id,
        ]);

        if ($cartItem->exists) {
            $newQuantity = $cartItem->quantity + $request->quantity;

            if ($newQuantity > $product->product_quantity) {
                return back()->with('error', 'Not enough stock available.');
            }

            $cartItem->quantity = $newQuantity;
        } else {
            $cartItem->quantity = $request->quantity;
        }

        $cartItem->save();

        return back()->with('success', 'Product added to cart!');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        // Ensure customer can only update their own cart
        if ($cartItem->customer_user_id !== auth('customer')->id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cartItem->product->product_quantity,
        ]);

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Cart updated!');
    }

    public function remove(CartItem $cartItem)
    {
        // Ensure customer can only remove from their own cart
        if ($cartItem->customer_user_id !== auth('customer')->id()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart!');
    }

    public function clear()
    {
        CartItem::where('customer_user_id', auth('customer')->id())->delete();

        return back()->with('success', 'Cart cleared!');
    }
}