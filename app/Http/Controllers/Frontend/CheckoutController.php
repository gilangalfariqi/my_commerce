<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Cart\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

/**
 * WhatsApp Fast Checkout Controller
 *
 * Web ini menggunakan model checkout via WhatsApp saja —
 * tidak ada payment gateway, tidak ada form alamat lengkap.
 * User memilih produk → cart → klik tombol → diarahkan ke WhatsApp penjual.
 */
class CheckoutController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    // ─────────────────────────────────────────────────────────────────────────
    // WhatsApp Fast Checkout
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Tampilkan halaman checkout ringkasan cart sebelum redirect ke WhatsApp.
     */
    public function whatsappFast(): mixed
    {
        $cart = $this->cartService->getCartWithItems();
        if ($cart->items->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        return view('frontend.checkout.whatsapp_fast', compact('cart'));
    }

    /**
     * Generate WhatsApp URL dan kembalikan sebagai JSON untuk redirect JS.
     *
     * Rate limiting: route sudah dilindungi oleh throttle middleware di routes/web.php.
     */
    public function whatsappFastLink(): JsonResponse
    {
        $cart = $this->cartService->getCartWithItems();
        if ($cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Keranjang belanja Anda masih kosong.'], 422);
        }

        $destination = $this->getWhatsAppNumber();

        if (empty($destination)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor WhatsApp toko belum dikonfigurasi. Silakan hubungi admin.',
            ], 500);
        }

        $message = $this->buildWhatsAppFastMessage($cart);
        $whatsAppUrl = 'https://wa.me/' . $destination . '?text=' . rawurlencode($message);

        return response()->json([
            'success'      => true,
            'redirect_url' => $whatsAppUrl,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Ambil nomor WhatsApp toko dari DB settings, lalu fallback ke config, lalu env.
     * Angka saja (tanpa +, -, spasi) seperti "6281234567890".
     */
    private function getWhatsAppNumber(): string
    {
        // Prioritas: DB setting → config → hardcoded fallback
        $raw = Setting::getValue('store_whatsapp')
            ?? config('services.whatsapp.checkout_number', '');

        return preg_replace('/\D+/', '', (string) $raw);
    }

    /**
     * Bangun pesan WhatsApp untuk fast checkout (tanpa form alamat).
     */
    private function buildWhatsAppFastMessage($cart): string
    {
        $storeName = Setting::getValue('store_name') ?? config('app.name', 'Toko Kami');

        $lines   = [];
        $lines[] = "Halo {$storeName}, saya ingin melakukan pemesanan untuk produk berikut:";
        $lines[] = '';
        $lines[] = 'Rincian Pesanan:';
        $lines[] = '-----------------------------------';

        foreach ($cart->items as $item) {
            $line = "- {$item->product->name}";
            if (! empty($item->variant?->name)) {
                $line .= " ({$item->variant->name})";
            }
            $line .= "\n  Jumlah: {$item->quantity}";
            $line .= "\n  Subtotal: Rp " . number_format((float) $item->getTotalPrice(), 0, ',', '.');
            $lines[] = $line;
        }

        $lines[] = '-----------------------------------';

        if ($cart->getDiscountAmount() > 0) {
            $lines[] = 'Diskon: Rp ' . number_format((float) $cart->getDiscountAmount(), 0, ',', '.');
        }

        $lines[] = '*Total Pesanan: Rp ' . number_format((float) $cart->getGrandTotal(), 0, ',', '.') . '*';
        $lines[] = '';
        $lines[] = 'Mohon informasi mengenai ketersediaan stok dan instruksi pembayaran selanjutnya.';
        $lines[] = 'Terima kasih.';

        return implode("\n", $lines);
    }
}
