<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShippingAddress;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Cart\CartService;
use App\Services\RajaOngkir\RajaOngkirService;
use App\Services\Midtrans\MidtransService;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected RajaOngkirService $rajaOngkirService;
    protected MidtransService $midtransService;

    public function __construct(
        CartService $cartService,
        RajaOngkirService $rajaOngkirService,
        MidtransService $midtransService
    ) {
        $this->cartService = $cartService;
        $this->rajaOngkirService = $rajaOngkirService;
        $this->midtransService = $midtransService;
    }

    public function index(): mixed
    {
        $cart = $this->cartService->getCartWithItems();
        if ($cart->items->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty.');
        }

        $provinces = $this->rajaOngkirService->getProvinces();
        $weightGrams = $cart->getTotalWeight();

        return view('frontend.checkout.index', compact('cart', 'provinces', 'weightGrams'));
    }

    public function getCities(Request $request): JsonResponse
    {
        $provinceId = $request->get('province_id');
        $cities = $this->rajaOngkirService->getCities($provinceId);
        return response()->json($cities);
    }

    public function calculateShipping(Request $request): JsonResponse
    {
        $request->validate([
            'city_id' => 'required|integer',
            'courier' => 'required|string|in:jne,pos,tiki',
        ]);

        $cart = $this->cartService->getCartWithItems();
        $weightGrams = $cart->getTotalWeight();
        $originCityId = config('rajaongkir.origin_city_id');

        $costs = $this->rajaOngkirService->calculateShipping(
            $originCityId,
            $request->city_id,
            $weightGrams,
            $request->courier
        );

        return response()->json($costs);
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
            'courier' => 'required|string|in:jne,pos,tiki',
            'shipping_service' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        $cart = $this->cartService->getCartWithItems();
        if ($cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        // Server-side recalculation to avoid frontend manipulation
        $originCityId = config('rajaongkir.origin_city_id');
        $weightGrams = $cart->getTotalWeight();
        
        $shippingCosts = $this->rajaOngkirService->calculateShipping(
            $originCityId,
            $request->city_id,
            $weightGrams,
            $request->courier
        );

        $validatedCost = null;
        foreach ($shippingCosts as $costOption) {
            if ($costOption['service'] === $request->shipping_service) {
                $validatedCost = $costOption['cost'][0]['value'] ?? null;
                break;
            }
        }

        if ($validatedCost === null || (float)$validatedCost !== (float)$request->shipping_cost) {
            return response()->json(['success' => false, 'message' => 'Invalid shipping calculation. Please try again.'], 422);
        }

        // Stock check before ordering
        foreach ($cart->items as $item) {
            if ($item->product_variant_id) {
                if ($item->variant->stock < $item->quantity) {
                    return response()->json(['success' => false, 'message' => "Insufficient stock for product variant: {$item->product->name} ({$item->variant->name})"], 422);
                }
            } else {
                if ($item->product->stock < $item->quantity) {
                    return response()->json(['success' => false, 'message' => "Insufficient stock for product: {$item->product->name}"], 422);
                }
            }
        }

        try {
            $order = DB::transaction(function () use ($request, $cart, $validatedCost) {
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
                $grandTotal = $subtotal - $discount + $validatedCost;

                // Create Order
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'status' => OrderStatus::PENDING,
                    'total_amount' => $subtotal,
                    'discount_amount' => $discount,
                    'shipping_amount' => $validatedCost,
                    'grand_total' => $grandTotal,
                    'courier' => $request->courier,
                    'shipping_service' => $request->shipping_service,
                    'notes' => $request->notes,
                ]);

                // Order Items
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

                // Shipping Address
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

                // Payment placeholder
                Payment::create([
                    'order_id' => $order->id,
                    'status' => PaymentStatus::PENDING,
                    'amount' => $grandTotal,
                ]);

                // Clear cart items
                $cart->items()->delete();
                $cart->update(['coupon_id' => null]);

                return $order;
            });

            // Generate Snap token
            $snapToken = $this->midtransService->createSnapToken($order);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully!',
                'redirect_url' => route('orders.show', $order->order_number),
                'snap_token' => $snapToken,
            ]);

        } catch (\Exception $e) {
            Log::error('Order creation transaction failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create order. Please try again.'], 500);
        }
    }
}
