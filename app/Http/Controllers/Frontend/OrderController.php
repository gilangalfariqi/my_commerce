<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Frontend Order Controller
 *
 * Menampilkan riwayat pesanan user dan detail pesanan.
 * Alur pembayaran menggunakan WhatsApp Checkout — tidak ada Midtrans/payment gateway.
 */
class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items'])
            ->latest()
            ->paginate(10);

        return view('frontend.orders.index', compact('orders'));
    }

    public function show(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['items.product.primaryImage', 'shippingAddress'])
            ->firstOrFail();

        // Buat link WhatsApp untuk menghubungi toko terkait pesanan ini
        $rawWaNumber = \App\Models\Setting::getValue('store_whatsapp') ?? config('services.whatsapp.number', '');
        $waNumber    = preg_replace('/\D+/', '', (string) $rawWaNumber);
        $waContactUrl = $waNumber
            ? 'https://wa.me/' . $waNumber . '?text=' . rawurlencode("Halo, saya ingin menanyakan status pesanan *{$orderNumber}*.")
            : null;

        return view('frontend.orders.show', compact('order', 'waContactUrl'));
    }

    /**
     * Order tracking publik — dilindungi throttle:5,1 di routes/web.php
     * untuk mencegah enumerasi nomor pesanan.
     */
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
}
