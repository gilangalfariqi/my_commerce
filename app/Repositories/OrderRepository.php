<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShippingAddress;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ActivityLog;
use App\DTOs\CheckoutDTO;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderRepository implements OrderRepositoryInterface
{
    public function createFromCart(Cart $cart, CheckoutDTO $checkoutDto): Order
    {
        return DB::transaction(function () use ($cart, $checkoutDto) {
            $subtotal = $cart->getSubtotal();
            $discount = $cart->getDiscountAmount();
            $shippingCost = $checkoutDto->shippingCost;
            $grandTotal = max(0.0, ($subtotal - $discount) + $shippingCost);

            // 1. Create the Order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $cart->user_id,
                'status' => OrderStatus::PENDING,
                'total_amount' => $subtotal,
                'discount_amount' => $discount,
                'shipping_amount' => $shippingCost,
                'grand_total' => $grandTotal,
                'coupon_id' => $cart->coupon_id,
                'notes' => $checkoutDto->notes,
                'courier' => $checkoutDto->courier,
                'weight' => $cart->getTotalWeight(),
            ]);

            // 2. Create Order Items and update stocks
            foreach ($cart->items as $cartItem) {
                $unitPrice = $cartItem->getUnitPrice();
                $itemTotal = $cartItem->quantity * $unitPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'product_name' => $cartItem->product->name,
                    'variant_name' => $cartItem->variant?->name,
                    'quantity' => $cartItem->quantity,
                    'price' => $unitPrice,
                    'total' => $itemTotal,
                ]);

                // Update stock
                if ($cartItem->product_variant_id) {
                    $variant = ProductVariant::findOrFail($cartItem->product_variant_id);
                    if ($variant->stock < $cartItem->quantity) {
                        throw new Exception("Product variant '{$variant->name}' is out of stock.");
                    }
                    $variant->decrement('stock', $cartItem->quantity);
                } else {
                    $product = Product::findOrFail($cartItem->product_id);
                    if ($product->stock < $cartItem->quantity) {
                        throw new Exception("Product '{$product->name}' is out of stock.");
                    }
                    $product->decrement('stock', $cartItem->quantity);
                }
            }

            // 3. Create Shipping Address snapshot
            $shippingAddressData = array_merge($checkoutDto->toShippingAddressArray(), [
                'order_id' => $order->id,
            ]);
            ShippingAddress::create($shippingAddressData);

            // 4. Create Payment record
            Payment::create([
                'order_id' => $order->id,
                'payment_type' => 'pending',
                'payment_method' => $checkoutDto->paymentMethod,
                'status' => PaymentStatus::PENDING,
                'amount' => $grandTotal,
            ]);

            // 5. Increment coupon usage count
            if ($cart->coupon) {
                $cart->coupon->increment('usage_count');
            }

            // 6. Log Activity
            ActivityLog::log(
                'order_created',
                "Order {$order->order_number} created with total value " . number_format($grandTotal, 2) . ".",
                $cart->user_id
            );

            // 7. Clear Cart
            $cart->items()->delete();
            $cart->delete();

            return $order;
        });
    }

    public function findById(string $id): ?Order
    {
        return Order::with(['items.product', 'payment', 'shippingAddress', 'user'])->find($id);
    }

    public function findByNumber(string $orderNumber): ?Order
    {
        return Order::with(['items.product', 'payment', 'shippingAddress', 'user'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function getByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Order::with(['payment'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function updateStatus(string $orderId, OrderStatus $status): bool
    {
        $order = Order::findOrFail($orderId);
        $oldStatus = $order->status->value;
        $order->status = $status;
        $saved = $order->save();

        if ($saved) {
            ActivityLog::log(
                'order_status_updated',
                "Order {$order->order_number} status updated from '{$oldStatus}' to '{$status->value}'."
            );
        }

        return $saved;
    }
}
