<?php

namespace App\Services\Cart;

use App\DTOs\CartItemDTO;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartService
{
    public function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => null]
            );
        }

        $sessionId = Session::get('cart_session_id');
        if (!$sessionId) {
            $sessionId = Str::uuid()->toString();
            Session::put('cart_session_id', $sessionId);
        }

        return Cart::firstOrCreate(
            ['session_id' => $sessionId, 'user_id' => null],
            ['session_id' => $sessionId]
        );
    }

    public function addItem(CartItemDTO $dto): CartItem
    {
        $cart = $this->getOrCreateCart();

        // Backend stock validation — never trust frontend
        $product = Product::active()->findOrFail($dto->productId);

        if ($dto->productVariantId) {
            $variant = ProductVariant::where('product_id', $product->id)
                ->findOrFail($dto->productVariantId);
            if ($variant->stock < $dto->quantity) {
                throw new \Exception('Insufficient stock for this variant.');
            }
        } else {
            if ($product->stock < $dto->quantity) {
                throw new \Exception('Insufficient stock for this product.');
            }
        }

        $existingItem = $cart->items()
            ->where('product_id', $dto->productId)
            ->where('product_variant_id', $dto->productVariantId)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $dto->quantity);
            return $existingItem->fresh();
        }

        return CartItem::create([
            'cart_id'            => $cart->id,
            'product_id'         => $dto->productId,
            'product_variant_id' => $dto->productVariantId,
            'quantity'           => $dto->quantity,
        ]);
    }

    public function updateItemQuantity(int $cartItemId, int $quantity): void
    {
        $cart = $this->getOrCreateCart();
        $item = $cart->items()->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $item->delete();
            return;
        }

        $item->update(['quantity' => $quantity]);
    }

    public function removeItem(int $cartItemId): void
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->where('id', $cartItemId)->delete();
    }

    public function applyCoupon(string $code): array
    {
        $cart    = $this->getOrCreateCart();
        $coupon  = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon || !$coupon->isValidForAmount($cart->getSubtotal())) {
            return ['success' => false, 'message' => 'Invalid or expired coupon code.'];
        }

        $cart->update(['coupon_id' => $coupon->id]);

        return [
            'success'  => true,
            'message'  => 'Coupon applied successfully!',
            'discount' => $coupon->calculateDiscount($cart->getSubtotal()),
        ];
    }

    public function removeCoupon(): void
    {
        $cart = $this->getOrCreateCart();
        $cart->update(['coupon_id' => null]);
    }

    public function getCartWithItems(): Cart
    {
        $cart = $this->getOrCreateCart();
        return $cart->load(['items.product.primaryImage', 'items.variant', 'coupon']);
    }

    public function getItemCount(): int
    {
        $cart = $this->getOrCreateCart();
        return (int) $cart->items()->sum('quantity');
    }

    public function mergeGuestCartOnLogin(int $userId): void
    {
        $sessionId = Session::get('cart_session_id');
        if (!$sessionId) return;

        $guestCart = Cart::where('session_id', $sessionId)->where('user_id', null)->first();
        if (!$guestCart) return;

        $userCart = Cart::firstOrCreate(['user_id' => $userId], ['session_id' => null]);

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()
                ->where('product_id', $item->product_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
            } else {
                CartItem::create([
                    'cart_id'            => $userCart->id,
                    'product_id'         => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity'           => $item->quantity,
                ]);
            }
        }

        $guestCart->items()->delete();
        $guestCart->delete();
        Session::forget('cart_session_id');
    }
}
