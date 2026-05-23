<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'payment']);

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15);
        $statuses = OrderStatus::cases();

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'payment', 'items.product.primaryImage', 'shippingAddress']);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $order->load(['user', 'payment', 'shippingAddress']);
        $statuses = OrderStatus::cases();
        return view('admin.orders.edit', compact('order', 'statuses'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string',
            'shipping_tracking_number' => 'nullable|string|max:100',
        ]);

        $oldStatus = $order->status->value;
        $order->update([
            'status' => $request->status,
            'shipping_tracking_number' => $request->shipping_tracking_number,
        ]);

        if ($oldStatus !== $request->status) {
            ActivityLog::log(
                'order_status_updated',
                "Order {$order->order_number} status updated from {$oldStatus} to {$request->status} by Admin."
            );
        }

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order updated successfully.');
    }
}
