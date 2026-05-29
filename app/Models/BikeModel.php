<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $bike_brand_id
 * @property string $name
 * @property string $slug
 * @property string|null $engine_cc
 * @property string|null $engine_type
 * @property array|null $years_available
 * @property bool $is_active
 * @property int $sort_order
 */
class BikeModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'bike_brand_id',
        'name',
        'slug',
        'engine_cc',
        'engine_type',
        'years_available',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'years_available' => 'array',
        'is_active'       => 'boolean',
        'sort_order'      => 'integer',
    ];

    // ─────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────

    public function bikeBrand(): BelongsTo
    {
        return $this->belongsTo(BikeBrand::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_fitments', 'bike_model_id', 'product_id')
            ->withPivot(['year', 'notes'])
            ->withTimestamps();
    }

    // ─────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForBrand(Builder $query, int|BikeBrand $brand): Builder
    {
        $brandId = $brand instanceof BikeBrand ? $brand->id : $brand;

        return $query->where('bike_brand_id', $brandId);
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    /**
     * Returns a sorted, de-duplicated list of all available years for this model.
     *
     * Merges years from the `years_available` JSON column with any years
     * recorded in the pivot `product_fitments` table.
     */
    public function getAllAvailableYears(): array
    {
        $base = $this->years_available ?? [];

        $pivot = $this->products()
            ->select('product_fitments.year')
            ->whereNotNull('product_fitments.year')
            ->pluck('product_fitments.year')
            ->toArray();

        $merged = array_unique(array_merge($base, $pivot));
        rsort($merged); // newest first

        return $merged;
    }

    // ─────────────────────────────────────────
    // Route Model Binding
    // ─────────────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
