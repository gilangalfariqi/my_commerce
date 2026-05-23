<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'compare_at_price',
        'cost_price',
        'weight',
        'stock',
        'is_active',
        'is_featured',
        'views_count',
        'metadata',
    ];

    protected $casts = [
        'price' => 'float',
        'compare_at_price' => 'float',
        'cost_price' => 'float',
        'weight' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'metadata' => 'array',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function getCategoryAttribute(): ?Category
    {
        return $this->categories->first();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('name', 'asc');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getDiscountPercentAttribute(): int
    {
        if ($this->compare_at_price && $this->compare_at_price > $this->price) {
            return (int) round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
        }
        return 0;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_at_price && $this->compare_at_price > $this->price;
    }

    public function getFinalPrice(): float
    {
        // Check active flash sales
        $activeFlashSaleItem = $this->getActiveFlashSaleItem();
        if ($activeFlashSaleItem) {
            return (float) $activeFlashSaleItem->discounted_price;
        }

        return $this->price;
    }

    public function getActiveFlashSaleItem()
    {
        // Dynamic check of active flash sales
        // To avoid direct circular import issues, we query via DB or lazy relation
        return FlashSaleItem::where('product_id', $this->id)
            ->whereHas('flashSale', function ($query) {
                $query->where('is_active', true)
                    ->where('start_time', '<=', now())
                    ->where('end_time', '>=', now());
            })
            ->whereColumn('stock_sold', '<', 'stock_limit')
            ->first();
    }
}
