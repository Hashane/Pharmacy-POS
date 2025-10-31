<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Order\DataTables\OrderDataTable;
use Modules\Order\Entities\Order;

class AdminOrderController extends Controller
{
    public function index(OrderDataTable $dataTable)
    {
        abort_if(Gate::denies('access_products'), 403);

        return $dataTable->render('order::admin.orders.index');
    }

    public function show(Order $order)
    {
        $order->load(['customerUser', 'items.product.media']);

        return view('Order::admin.orders.show', compact('order'));
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