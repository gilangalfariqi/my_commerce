<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Pivot model for the product_fitments table.
 *
 * Represents the specific vehicle compatibility record linking
 * a Product to a BikeModel for a given production year.
 *
 * @property int $id
 * @property int $product_id
 * @property int $bike_model_id
 * @property int|null $year
 * @property string|null $notes
 */
class ProductFitment extends Pivot
{
    public $incrementing = true;

    protected $table = 'product_fitments';

    protected $fillable = [
        'product_id',
        'bike_model_id',
        'year',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    // ─────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function bikeModel(): BelongsTo
    {
        return $this->belongsTo(BikeModel::class);
    }
}
