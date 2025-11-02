<?php

namespace Modules\Order\Http\Controllers;
;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Order\Entities\CartItem;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderItem;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('customer_id', auth('customer')->id())
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return view('order::orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Ensure customer can only view their own orders
        if ($order->customer_id !== auth('customer')->id()) {
            abort(403);
        }

        $order->load('items.product.media');


        return view('order::orders.show', compact('order'));
    }

    public function checkout()
    {
        $cartItems = CartItem::where('customer_id', auth('customer')->id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Your cart is empty!');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->product_price * $item->quantity;
        });

        return view('order::orders.checkout', compact('cartItems', 'total'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $cartItems = CartItem::where('customer_id', auth('customer')->id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Your cart is empty!');
        }

        DB::beginTransaction();
        try {
            // Check stock availability
            foreach ($cartItems as $cartItem) {
                if ($cartItem->product->product_quantity < $cartItem->quantity) {
                    return back()->with('error', 'Not enough stock for ' . $cartItem->product->product_name);
                }
            }

            $totalAmount = $cartItems->sum(function ($item) {
                return $item->product->product_price * $item->quantity;
            });

            $order = Order::create([
                'customer_id' => auth('customer')->id(),
                'reference' => Order::generateReference(),
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->product_price,
                    'unit_price' => $cartItem->product->product_price,
                    'sub_total' => $cartItem->product->product_price * $cartItem->quantity,
                ]);
            }

            // Clear cart
            CartItem::where('customer_id', auth('customer')->id())->delete();

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully! Reference: ' . $order->reference);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}