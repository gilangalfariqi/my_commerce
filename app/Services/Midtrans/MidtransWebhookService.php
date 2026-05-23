<?php

namespace App\Services\Midtrans;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ActivityLog;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;

class MidtransWebhookService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
    }

    public function handle(array $payload): void
    {
        // Validate Midtrans signature
        $signatureKey = hash('sha512',
            $payload['order_id'] .
            $payload['status_code'] .
            $payload['gross_amount'] .
            config('midtrans.server_key')
        );

        if ($signatureKey !== ($payload['signature_key'] ?? '')) {
            Log::warning('Midtrans webhook: invalid signature', ['order_id' => $payload['order_id'] ?? null]);
            throw new \Exception('Invalid webhook signature.');
        }

        $orderNumber       = $payload['order_id'];
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus       = $payload['fraud_status'] ?? null;
        $transactionId     = $payload['transaction_id'];

        $order = Order::where('order_number', $orderNumber)->with(['payment', 'items'])->first();

        if (!$order) {
            Log::warning('Midtrans webhook: order not found', ['order_number' => $orderNumber]);
            return;
        }

        $payment = $order->payment;
        if (!$payment) {
            Log::warning('Midtrans webhook: payment not found', ['order_number' => $orderNumber]);
            return;
        }

        // Idempotency — skip already-settled payments
        if ($payment->transaction_id === $transactionId && $payment->status === PaymentStatus::SETTLED) {
            Log::info('Midtrans webhook: already processed', ['transaction_id' => $transactionId]);
            return;
        }

        // Update raw response
        $payment->update([
            'transaction_id' => $transactionId,
            'payment_type'   => $payload['payment_type'] ?? $payment->payment_type,
            'raw_response'   => $payload,
        ]);

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            if ($fraudStatus === null || $fraudStatus === 'accept') {
                $payment->update(['status' => PaymentStatus::SETTLED]);
                $order->update(['status' => OrderStatus::PROCESSING]);
                ActivityLog::log('payment_settled', "Payment settled for order {$order->order_number}.");
            } elseif ($fraudStatus === 'challenge') {
                $payment->update(['status' => PaymentStatus::PENDING]);
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $payment->update(['status' => PaymentStatus::EXPIRED]);
            $order->update(['status' => OrderStatus::CANCELLED]);
            $this->restoreStock($order);
        } elseif ($transactionStatus === 'refund') {
            $payment->update(['status' => PaymentStatus::REFUNDED]);
        } elseif ($transactionStatus === 'failure') {
            $payment->update(['status' => PaymentStatus::FAILED]);
        }

        Log::info('Midtrans webhook processed', [
            'order_number'       => $orderNumber,
            'transaction_status' => $transactionStatus,
        ]);
    }

    private function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_variant_id) {
                ProductVariant::where('id', $item->product_variant_id)->increment('stock', $item->quantity);
            } else {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }
        }
    }
}
