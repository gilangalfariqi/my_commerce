<?php

namespace App\Services\Midtrans;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized  = config('midtrans.is_sanitized', true);
        Config::$is3ds        = config('midtrans.is_3ds', true);
    }

    public function createSnapToken(Order $order): string
    {
        $order->load(['shippingAddress', 'items', 'payment']);
        $address = $order->shippingAddress;

        $customerDetails = [
            'first_name' => $address?->first_name ?? 'Customer',
            'last_name'  => $address?->last_name  ?? '',
            'email'      => $address?->email       ?? $order->user?->email,
            'phone'      => $address?->phone       ?? '',
            'billing_address' => [
                'address'     => $address?->address_line,
                'city'        => $address?->city_name,
                'postal_code' => $address?->postal_code,
                'country_code'=> 'IDN',
            ],
            'shipping_address' => [
                'address'     => $address?->address_line,
                'city'        => $address?->city_name,
                'postal_code' => $address?->postal_code,
                'country_code'=> 'IDN',
            ],
        ];

        $itemDetails = $order->items->map(fn ($item) => [
            'id'       => $item->product_id,
            'price'    => (int) round($item->price),
            'quantity' => $item->quantity,
            'name'     => mb_substr($item->product_name . ($item->variant_name ? ' - ' . $item->variant_name : ''), 0, 50),
        ])->toArray();

        if ($order->shipping_amount > 0) {
            $itemDetails[] = [
                'id'       => 'SHIPPING',
                'price'    => (int) round($order->shipping_amount),
                'quantity' => 1,
                'name'     => 'Ongkos Kirim (' . strtoupper($order->courier ?? '') . ')',
            ];
        }

        if ($order->discount_amount > 0) {
            $itemDetails[] = [
                'id'       => 'DISCOUNT',
                'price'    => -(int) round($order->discount_amount),
                'quantity' => 1,
                'name'     => 'Diskon',
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) round($order->grand_total),
            ],
            'customer_details' => $customerDetails,
            'item_details'     => $itemDetails,
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $order->payment?->update(['snap_token' => $snapToken]);
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap token creation failed', [
                'order_number' => $order->order_number,
                'error'        => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getClientKey(): string
    {
        return config('midtrans.client_key');
    }
}
