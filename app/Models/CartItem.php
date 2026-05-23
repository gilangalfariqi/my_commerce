<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getUnitPrice(): float
    {
        if ($this->variant) {
            return $this->variant->getFinalPrice();
        }

        return $this->product->getFinalPrice();
    }

    public function getWeight(): int
    {
        if ($this->variant && $this->variant->weight > 0) {
            return $this->variant->weight;
        }

        return $this->product->weight > 0 ? $this->product->weight : 1000; // default 1kg fallback
    }

    public function getTotalPrice(): float
    {
        return $this->quantity * $this->getUnitPrice();
    }

    public function getPriceAttribute(): float
    {
        return $this->getUnitPrice();
    }
}
