<?php

namespace Modules\Customer\Http\Controllers;
use App\Http\Controllers\Controller;
use Modules\Order\Entities\Order;
use Modules\Prescription\Entities\Prescription;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $customer = auth('customer')->user();

        $orders = Order::where('customer_id', $customer->id)
            ->latest()
            ->take(5)
            ->get();

        $prescriptions = Prescription::where('customer_id', $customer->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_orders' => Order::where('customer_id', $customer->id)->count(),
            'pending_orders' => Order::where('customer_id', $customer->id)->where('status', 'pending')->count(),
            'ready_orders' => Order::where('customer_id', $customer->id)->where('status', 'ready')->count(),
            'total_prescriptions' => Prescription::where('customer_id', $customer->id)->count(),
            'pending_prescriptions' => Prescription::where('customer_id', $customer->id)->where('status', 'pending')->count(),
        ];

        return view('customer::dashboard.index', compact('orders', 'prescriptions', 'stats'));
    }
}