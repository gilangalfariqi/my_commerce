<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_id',
        'shipping_method',
        'shipping_cost',
        'notes',
    ];

    protected $casts = [
        'shipping_cost' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function getSubtotal(): float
    {
        return $this->items->sum(function (CartItem $item) {
            return $item->quantity * $item->getUnitPrice();
        });
    }

    public function getDiscountAmount(): float
    {
        if (!$this->coupon) {
            return 0.0;
        }

        $subtotal = $this->getSubtotal();
        return $this->coupon->calculateDiscount($subtotal);
    }

    public function getGrandTotal(): float
    {
        $subtotal = $this->getSubtotal();
        $discount = $this->getDiscountAmount();
        $shipping = $this->shipping_cost ?? 0.0;

        return max(0.0, ($subtotal - $discount) + $shipping);
    }

    public function getTotalWeight(): int
    {
        return $this->items->sum(function (CartItem $item) {
            return $item->quantity * $item->getWeight();
        });
    }
}
