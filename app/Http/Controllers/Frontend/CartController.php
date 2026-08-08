<?php

namespace App\Http\Controllers\Frontend;

use App\DTOs\CartItemDTO;
use App\Http\Controllers\Controller;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(): JsonResponse
    {
        $cart = $this->cartService->getCartWithItems();
        return response()->json([
            'items' => $cart->items->map(fn($item) => [
                'id'                => $item->id,
                'product_id'        => $item->product_id,
                'product_name'      => $item->product->name,
                'product_slug'      => $item->product->slug,
                'product_url'       => route('products.show', $item->product->slug),
                'variant_id'        => $item->product_variant_id,
                'variant_name'      => $item->variant?->name,
                'quantity'          => $item->quantity,
                'price'             => (float)$item->getUnitPrice(),
                'formatted_price'   => number_format($item->getUnitPrice(), 0, ',', '.'),
                'subtotal'          => (float)($item->getTotalPrice()),
                'formatted_subtotal'=> number_format($item->getTotalPrice(), 0, ',', '.'),
                'image'             => $item->product->primaryImage?->url ?? asset('images/placeholder-product.webp'),
            ]),
            'subtotal'            => (float)$cart->getSubtotal(),
            'formatted_subtotal'  => number_format($cart->getSubtotal(), 0, ',', '.'),
            'discount'            => (float)$cart->getDiscountAmount(),
            'formatted_discount'  => number_format($cart->getDiscountAmount(), 0, ',', '.'),
            'grand_total'         => (float)$cart->getGrandTotal(),
            'formatted_grand_total' => number_format($cart->getGrandTotal(), 0, ',', '.'),
            'coupon_code'         => $cart->coupon?->code,
            'item_count'          => $this->cartService->getItemCount(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id'         => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity'           => 'required|integer|min:1|max:99',
        ]);

        try {
            $dto = new CartItemDTO(
                productId: (int) $request->product_id,
                productVariantId: $request->product_variant_id ? (int) $request->product_variant_id : null,
                quantity: (int) $request->quantity
            );

            $this->cartService->addItem($dto);

            return response()->json([
                'success'    => true,
                'message'    => 'Produk berhasil ditambahkan ke keranjang!',
                'item_count' => $this->cartService->getItemCount(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update quantity item keranjang.
     *
     * Security: CartService::updateItemQuantity() harus memastikan item
     * tersebut milik cart/session user yang sedang aktif (IDOR protection).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:99',
        ]);

        try {
            // CartService bertanggung jawab memvalidasi ownership item
            $this->cartService->updateItemQuantity($id, (int) $request->quantity);

            return response()->json([
                'success'    => true,
                'message'    => 'Keranjang berhasil diperbarui!',
                'item_count' => $this->cartService->getItemCount(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() === 403 ? 403 : 422);
        }
    }

    /**
     * Hapus item dari keranjang.
     *
     * Security: CartService::removeItem() harus memvalidasi ownership item.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // CartService bertanggung jawab memvalidasi ownership item
            $this->cartService->removeItem($id);

            return response()->json([
                'success'    => true,
                'message'    => 'Item berhasil dihapus dari keranjang!',
                'item_count' => $this->cartService->getItemCount(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() === 403 ? 403 : 422);
        }
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50|alpha_dash',
        ]);

        $result = $this->cartService->applyCoupon($request->code);

        return response()->json($result);
    }

    public function removeCoupon(): JsonResponse
    {
        $this->cartService->removeCoupon();
        return response()->json([
            'success' => true,
            'message' => 'Kupon berhasil dihapus.',
        ]);
    }
}
