<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\Entities\Order;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customerUser', 'items.product']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by reference or customer name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'like', '%' . $request->search . '%')
                    ->orWhereHas('customerUser', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.customer-orders::index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['customerUser', 'items.product.media']);

        return view('admin.customer-orders::show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        if ($request->status === 'ready' && $order->status !== 'ready') {
            $updateData['ready_at'] = now();
        }

        if ($request->status === 'completed' && $order->status !== 'completed') {
            $updateData['completed_at'] = now();
        }

        $order->update($updateData);

        return back()->with('success', 'Order status updated successfully!');
    }
}