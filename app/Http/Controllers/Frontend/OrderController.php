<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['payment', 'items'])
            ->latest()
            ->paginate(10);

        return view('frontend.orders.index', compact('orders'));
    }

    public function show(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['payment', 'items.product.primaryImage', 'shippingAddress'])
            ->firstOrFail();

        return view('frontend.orders.show', compact('order'));
    }

    public function track(Request $request): View
    {
        $order = null;
        if ($request->filled('order_number')) {
            $order = Order::where('order_number', $request->order_number)
                ->with(['items', 'shippingAddress'])
                ->first();
        }

        return view('frontend.orders.track', compact('order'));
    }

    public function syncStatus(string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with('payment')
            ->firstOrFail();

        $serverKey = config('midtrans.server_key');
        $isProduction = config('midtrans.is_production', false);
        $baseUrl = $isProduction 
            ? 'https://api.midtrans.com/v2' 
            : 'https://api.sandbox.midtrans.com/v2';

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->get("{$baseUrl}/{$orderNumber}/status");

            if ($response->successful()) {
                $statusData = $response->json();
                $transactionStatus = $statusData['transaction_status'] ?? null;
                $fraudStatus = $statusData['fraud_status'] ?? null;
                $payment = $order->payment;

                if ($payment) {
                    if (in_array($transactionStatus, ['capture', 'settlement'])) {
                        if ($fraudStatus === null || $fraudStatus === 'accept') {
                            $payment->update(['status' => PaymentStatus::SETTLED]);
                            $order->update(['status' => OrderStatus::PROCESSING]);
                        }
                    } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                        $payment->update(['status' => PaymentStatus::EXPIRED]);
                        $order->update(['status' => OrderStatus::CANCELLED]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'status' => $order->status->value,
                    'payment_status' => $payment?->status->value,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Order sync status failed for {$orderNumber}: " . $e->getMessage());
        }

        return response()->json(['success' => false, 'message' => 'Unable to sync status at this time.'], 500);
    }
}
