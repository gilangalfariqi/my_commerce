<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'stock',
        'weight',
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'weight' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFinalPrice(): float
    {
        // Variant price overrides product price if present
        if ($this->price && $this->price > 0) {
            return $this->price;
        }

        return $this->product->getFinalPrice();
    }
}
