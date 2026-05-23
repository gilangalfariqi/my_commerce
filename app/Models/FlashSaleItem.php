<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleItem extends Model
{
    use HasFactory;

    // By default, since the migration didn't have soft deletes, this works as a standard model
    protected $fillable = [
        'flash_sale_id',
        'product_id',
        'discounted_price',
        'stock_limit',
        'stock_sold',
        'order_limit',
    ];

    protected $casts = [
        'discounted_price' => 'float',
        'stock_limit' => 'integer',
        'stock_sold' => 'integer',
        'order_limit' => 'integer',
    ];

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
