<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;

use App\Services\Cart\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function index(): mixed
    {
        $cart = $this->cartService->getCartWithItems();
        if ($cart->items->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty.');
        }

        $provinces = [];
        $weightGrams = $cart->getTotalWeight();

        return view('frontend.checkout.index', compact('cart', 'provinces', 'weightGrams'));
    }

    // WhatsApp fast checkout: no payment, no address form.
    public function whatsappFast(): mixed
    {
        $cart = $this->cartService->getCartWithItems();
        if ($cart->items->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty.');
        }

        return view('frontend.checkout.whatsapp_fast', compact('cart'));
    }

    public function whatsappFastLink(): JsonResponse
    {
        $cart = $this->cartService->getCartWithItems();
        if ($cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $destination = $settings->get('store_whatsapp') ?? '6282174128947';

        $destination = preg_replace('/\D+/', '', (string) $destination);
        if (empty($destination)) {
            return response()->json(['success' => false, 'message' => 'WhatsApp checkout number is not configured.'], 500);
        }

        $message = $this->buildWhatsAppFastMessage($cart);
        $whatsAppUrl = 'https://wa.me/' . $destination . '?text=' . rawurlencode($message);

        return response()->json([
            'success' => true,
            'redirect_url' => $whatsAppUrl,
        ]);
    }

    private function buildWhatsAppFastMessage($cart): string
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $storeName = $settings->get('store_name') ?? 'MotoPartHub';
        $lines = [];
        $lines[] = "Halo {$storeName}, saya ingin melakukan pemesanan via WhatsApp Checkout (tanpa payment/alamat).";
        $lines[] = '';
        $lines[] = 'Rincian Pesanan:';

        foreach ($cart->items as $item) {
            $line = '- ' . $item->product->name;
            if (!empty($item->variant?->name)) {
                $line .= ' (OEM/Part: ' . $item->variant->name . ')';
            }
            $line .= '\n  Qty: ' . $item->quantity;
            $line .= '\n  Subtotal: Rp ' . number_format((float) $item->getTotalPrice(), 0, ',', '.');
            $lines[] = $line;
        }

        $lines[] = '';
        $lines[] = 'Total: Rp ' . number_format((float) $cart->getGrandTotal(), 0, ',', '.');

        return implode("\n", array_filter($lines));
    }



    // Kept for backward compatibility with older frontend assets/routes.
    public function getCities(Request $request): JsonResponse
    {
        return response()->json([]);
    }

    // Kept for backward compatibility with older frontend assets/routes.
    public function calculateShipping(Request $request): JsonResponse
    {
        return response()->json([]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'address_line' => 'required|string',
            'province_id' => 'required|integer',
            'province_name' => 'required|string',
            'city_id' => 'required|integer',
            'city_name' => 'required|string',
            'postal_code' => 'required|string|max:10',
            // Shipping fields may still be posted by UI; we store them but do not call RajaOngkir.
            'courier' => 'nullable|string|in:jne,pos,tiki',
            'shipping_service' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $cart = $this->cartService->getCartWithItems();
        if ($cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $shippingCost = (float) ($request->shipping_cost ?? 0);

        // Stock check before ordering
        foreach ($cart->items as $item) {
            if ($item->product_variant_id) {
                if ($item->variant->stock < $item->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product variant: {$item->product->name} ({$item->variant->name})",
                    ], 422);
                }
            } else {
                if ($item->product->stock < $item->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product: {$item->product->name}",
                    ], 422);
                }
            }
        }

        try {
            $order = DB::transaction(function () use ($request, $cart, $shippingCost) {
                // Decrement stocks
                foreach ($cart->items as $item) {
                    if ($item->product_variant_id) {
                        ProductVariant::where('id', $item->product_variant_id)
                            ->decrement('stock', $item->quantity);
                    } else {
                        Product::where('id', $item->product_id)
                            ->decrement('stock', $item->quantity);
                    }
                }

                $subtotal = $cart->getSubtotal();
                $discount = $cart->getDiscount();
                $grandTotal = $subtotal - $discount + $shippingCost;

                $orderedViaWhatsappStatus = 'ordered_via_whatsapp';
                $orderStatusValue = is_scalar(OrderStatus::PENDING ?? null) ? OrderStatus::PENDING : null;
                // If enum contains ordered_via_whatsapp, prefer it; otherwise use plain string.
                if (enum_exists(OrderStatus::class)) {
                    try {
                        $orderStatusValue = OrderStatus::from($orderedViaWhatsappStatus);
                    } catch (\Throwable $e) {
                        $orderStatusValue = null;
                    }
                }

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'status' => $orderStatusValue?->value ?? $orderedViaWhatsappStatus,
                    'total_amount' => $subtotal,
                    'discount_amount' => $discount,
                    'shipping_amount' => $shippingCost,
                    'grand_total' => $grandTotal,
                    'courier' => $request->courier,
                    'shipping_service' => $request->shipping_service,
                    'notes' => $request->notes,
                ]);

                foreach ($cart->items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'product_name' => $item->product->name,
                        'variant_name' => $item->variant?->name,
                        'quantity' => $item->quantity,
                        'price' => $item->getUnitPrice(),
                        'total' => $item->getTotalPrice(),
                    ]);
                }

                ShippingAddress::create([
                    'order_id' => $order->id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address_line' => $request->address_line,
                    'province_id' => $request->province_id,
                    'province_name' => $request->province_name,
                    'city_id' => $request->city_id,
                    'city_name' => $request->city_name,
                    'postal_code' => $request->postal_code,
                ]);

                // Clear cart
                $cart->items()->delete();
                $cart->update(['coupon_id' => null]);

                return $order;
            });

            // Build WhatsApp URL after order created
            $order->loadMissing(['items', 'shippingAddress']);
            $whatsAppUrl = $this->makeWhatsAppCheckoutUrl($order);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully! Redirecting to WhatsApp...',
                'redirect_url' => $whatsAppUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('Order creation transaction failed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Failed to create order. Please try again.'], 500);
        }
    }

    private function makeWhatsAppCheckoutUrl(Order $order): string
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $destination = $settings->get('store_whatsapp')
            ?? config('services.whatsapp.checkout_number')
            ?? env('WHATSAPP_CHECKOUT_NUMBER')
            ?? '6282174128947';

        $destination = preg_replace('/\D+/', '', (string) $destination);

        $message = $this->buildWhatsAppMessage($order);

        // WhatsApp deep link
        // https://wa.me/<number>?text=<url-encoded-text>
        return 'https://wa.me/' . $destination . '?text=' . rawurlencode($message);
    }

    private function buildWhatsAppMessage(Order $order): string
    {
        $shipping = $order->shippingAddress;

        $trackUrl = route('orders.show', $order->order_number);

        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $storeName = $settings->get('store_name') ?? 'MotoPartHub';

        $lines = [];
        $lines[] = "Halo {$storeName}, saya ingin melakukan pemesanan melalui WhatsApp Checkout.";
        $lines[] = '';
        $lines[] = 'Customer:';
        $lines[] = '- Nama: ' . ($shipping?->first_name . ' ' . $shipping?->last_name);
        $lines[] = '- No HP: ' . ($shipping?->phone);
        $lines[] = '- Alamat: ' . $shipping?->address_line . ', ' . $shipping?->city_name . ', ' . $shipping?->province_name . ' ' . $shipping?->postal_code;
        $lines[] = '';
        $lines[] = 'Rincian Pesanan:';

        foreach ($order->items as $item) {
            $lines[] = '- ' . $item->product_name;
            if (!empty($item->variant_name)) {
                $lines[] = '  (OEM/Part: ' . $item->variant_name . ')';
            }
            $lines[] = '  Qty: ' . $item->quantity;
            $lines[] = '  Subtotal: Rp ' . number_format((float) $item->total, 0, ',', '.');
        }

        $lines[] = '';
        $lines[] = 'Total: Rp ' . number_format((float) $order->grand_total, 0, ',', '.');
        $lines[] = '';
        $lines[] = 'Tracking Pesanan: ' . $trackUrl;

        return implode("\n", array_filter($lines));
    }
}

