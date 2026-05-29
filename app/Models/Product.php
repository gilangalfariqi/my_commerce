<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * MotoPartHub Product Model
 *
 * Integrates with:
 * - Laravel Scout (Meilisearch/array driver) via Searchable trait
 * - Spatie Media Library with WebP auto-conversion + multiple responsive sizes
 * - Spatie Laravel Query Builder via named scopes & filterable relations
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $sku
 * @property string|null $description
 * @property string|null $short_description
 * @property float $price
 * @property float|null $compare_at_price
 * @property float|null $cost_price
 * @property int $weight
 * @property int $stock
 * @property bool $is_active
 * @property bool $is_featured
 * @property int $views_count
 * @property array|null $metadata
 */
class Product extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use Searchable;
    use SoftDeletes;

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
        'price'            => 'float',
        'compare_at_price' => 'float',
        'cost_price'       => 'float',
        'weight'           => 'integer',
        'stock'            => 'integer',
        'is_active'        => 'boolean',
        'is_featured'      => 'boolean',
        'views_count'      => 'integer',
        'metadata'         => 'array',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Spatie Media Library — Collections & Conversions
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Register media collections and automated WebP conversions.
     *
     * Collections:
     *  - "product-images": main gallery (multiple files allowed, WebP forced)
     *
     * Conversions:
     *  - "thumb"    → 300×300 WebP  (used in catalog grid cards)
     *  - "medium"   → 600×600 WebP  (used in PDP thumbnail strip)
     *  - "hd"       → 1200×1200 WebP (used in PDP main zoom viewer)
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product-images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
            ->useFallbackUrl('/images/placeholder-product.webp')
            ->useFallbackPath(public_path('/images/placeholder-product.webp'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->format('webp')
            ->width(300)
            ->height(300)
            ->fit(\Spatie\Image\Enums\Fit::Crop)
            ->optimize()
            ->performOnCollections('product-images')
            ->nonQueued(); // synchronous for immediate availability

        $this->addMediaConversion('medium')
            ->format('webp')
            ->width(600)
            ->height(600)
            ->fit(\Spatie\Image\Enums\Fit::Contain)
            ->optimize()
            ->performOnCollections('product-images');

        $this->addMediaConversion('hd')
            ->format('webp')
            ->width(1200)
            ->height(1200)
            ->fit(\Spatie\Image\Enums\Fit::Contain)
            ->optimize()
            ->performOnCollections('product-images');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Laravel Scout — Meilisearch Indexing
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build the searchable document sent to Meilisearch.
     *
     * Flattens the fitment tree (brand name, model name, year) into top-level
     * arrays so that Meilisearch can index and filter them natively. This is
     * the industry-standard approach for automotive part catalog search.
     */
    public function toSearchableArray(): array
    {
        $this->loadMissing(['fitments.bikeModel.bikeBrand', 'categories']);

        // Flatten fitment data for Meilisearch filterable attributes
        $fitmentBrands = $this->fitments
            ->map(fn ($f) => $f->bikeModel?->bikeBrand?->name)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $fitmentModels = $this->fitments
            ->map(fn ($f) => $f->bikeModel?->name)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $fitmentYears = $this->fitments
            ->pluck('year')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Flattened string for full-text relevance (e.g. "Honda Vario 125 2020 2021")
        $fitmentText = implode(' ', array_merge($fitmentBrands, $fitmentModels, array_map('strval', $fitmentYears)));

        $categoryNames = $this->categories->pluck('name')->implode(', ');

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'sku'               => $this->sku,
            'short_description' => $this->short_description,
            'price'             => (float) $this->price,
            'compare_at_price'  => (float) ($this->compare_at_price ?? 0),
            'stock'             => $this->stock,
            'is_active'         => $this->is_active,
            'is_featured'       => $this->is_featured,

            // Filterable / facet attributes in Meilisearch
            'categories'        => $categoryNames,
            'fitment_brands'    => $fitmentBrands,
            'fitment_models'    => $fitmentModels,
            'fitment_years'     => $fitmentYears,

            // Full-text relevance booster
            'fitment_text'      => $fitmentText,

            'created_at'        => $this->created_at?->timestamp,
        ];
    }

    /**
     * Meilisearch configuration: filterable + sortable attributes.
     * These are pushed via `scout:sync-index-settings`.
     */
    public function searchableAs(): string
    {
        return 'products';
    }

    /**
     * Only index active products that are not soft-deleted.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->is_active && ! $this->trashed();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────────────────────

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('name', 'asc');
    }

    /** @deprecated Use Spatie Media Library instead. Kept for legacy fallback. */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    /** @deprecated Use Spatie Media Library instead. Kept for legacy fallback. */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Vehicle fitment records — the core of motorcycle part compatibility.
     * Joined through product_fitments pivot with year + notes.
     */
    public function fitments(): BelongsToMany
    {
        return $this->belongsToMany(
            BikeModel::class,
            'product_fitments',
            'product_id',
            'bike_model_id'
        )
            ->using(ProductFitment::class)
            ->withPivot(['year', 'notes'])
            ->withTimestamps()
            ->with('bikeBrand');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scopes (for Spatie Query Builder AllowedFilter::scope usage)
    // ─────────────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Filter by minimum price (used with AllowedFilter::scope).
     */
    public function scopeMinPrice(Builder $query, float $min): Builder
    {
        return $query->where('price', '>=', $min);
    }

    /**
     * Filter by maximum price (used with AllowedFilter::scope).
     */
    public function scopeMaxPrice(Builder $query, float $max): Builder
    {
        return $query->where('price', '<=', $max);
    }

    /**
     * Filter products compatible with a specific bike brand.
     * Works through the product_fitments → bike_models → bike_brands join.
     */
    public function scopeForBikeBrand(Builder $query, string|int $brandIdentifier): Builder
    {
        return $query->whereHas('fitments', function (Builder $q) use ($brandIdentifier) {
            $q->whereHas('bikeBrand', function (Builder $bq) use ($brandIdentifier) {
                if (is_numeric($brandIdentifier)) {
                    $bq->where('id', $brandIdentifier);
                } else {
                    $bq->where('slug', $brandIdentifier);
                }
            });
        });
    }

    /**
     * Filter products compatible with a specific bike model.
     */
    public function scopeForBikeModel(Builder $query, string|int $modelIdentifier): Builder
    {
        return $query->whereHas('fitments', function (Builder $q) use ($modelIdentifier) {
            if (is_numeric($modelIdentifier)) {
                $q->where('bike_model_id', $modelIdentifier);
            } else {
                $q->whereHas('bikeModel', fn (Builder $mq) => $mq->where('slug', $modelIdentifier));
            }
        });
    }

    /**
     * Filter products compatible with a specific production year.
     */
    public function scopeForFitmentYear(Builder $query, int $year): Builder
    {
        return $query->whereHas('fitments', function (Builder $q) use ($year) {
            $q->where(function (Builder $inner) use ($year) {
                // Exact year match OR null year (= fits all years for that model)
                $inner->where('year', $year)->orWhereNull('year');
            });
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Computed Attributes & Helpers
    // ─────────────────────────────────────────────────────────────────────────

    public function getCategoryAttribute(): ?Category
    {
        return $this->categories->first();
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
        return (bool) ($this->compare_at_price && $this->compare_at_price > $this->price);
    }

    public function getFinalPrice(): float
    {
        $activeFlashSaleItem = $this->getActiveFlashSaleItem();
        if ($activeFlashSaleItem) {
            return (float) $activeFlashSaleItem->discounted_price;
        }

        return $this->price;
    }

    public function getActiveFlashSaleItem(): ?FlashSaleItem
    {
        return FlashSaleItem::where('product_id', $this->id)
            ->whereHas('flashSale', function (Builder $query) {
                $query->where('is_active', true)
                    ->where('start_time', '<=', now())
                    ->where('end_time', '>=', now());
            })
            ->whereColumn('stock_sold', '<', 'stock_limit')
            ->first();
    }

    /**
     * Generates a pre-filled WhatsApp deep-link URL for this product.
     *
     * The message includes: product name, SKU/OEM number, price, and an
     * optional vehicle fitment context for precise customer inquiries.
     *
     * @param array{brand?: string, model?: string, year?: int|string}|null $fitmentContext
     */
    public function getWhatsAppLink(?array $fitmentContext = null): string
    {
        $waNumber = config('app.whatsapp_number', env('WHATSAPP_NUMBER', '6282174128947'));

        $productUrl = route('products.show', $this->slug);

        $message = "Halo, saya tertarik dengan produk:\n";
        $message .= "🔧 *{$this->name}*\n";
        $message .= "📦 SKU/OEM: `{$this->sku}`\n";
        $message .= "💰 Harga: Rp " . number_format($this->getFinalPrice(), 0, ',', '.') . "\n";

        if (! empty($fitmentContext)) {
            $message .= "\n🏍️ Kendaraan saya:\n";
            $message .= "   Merk  : " . ($fitmentContext['brand'] ?? '-') . "\n";
            $message .= "   Model : " . ($fitmentContext['model'] ?? '-') . "\n";
            $message .= "   Tahun : " . ($fitmentContext['year'] ?? '-') . "\n";
        }

        $message .= "\n🔗 {$productUrl}\n";
        $message .= "\nApakah sparepart ini tersedia dan cocok untuk kendaraan saya?";

        return 'https://wa.me/' . $waNumber . '?text=' . rawurlencode($message);
    }

    /**
     * Get the primary WebP thumbnail URL (300x300) for catalog grid display.
     * Falls back to legacy ProductImage if Spatie media is not yet populated.
     */
    public function getThumbnailUrlAttribute(): string
    {
        $spatiaThumb = $this->getFirstMediaUrl('product-images', 'thumb');

        if ($spatiaThumb) {
            return $spatiaThumb;
        }

        // Legacy fallback
        $primary = $this->primaryImage;
        if ($primary) {
            if (filter_var($primary->image_path, FILTER_VALIDATE_URL)) {
                return $primary->image_path;
            }

            return \Illuminate\Support\Facades\Storage::disk('public')->url($primary->image_path);
        }

        return asset('images/placeholder-product.webp');
    }

    /**
     * Build a human-readable fitment summary string.
     * Example: "Honda Vario 125 (2019–2022), Honda Beat (2020)"
     */
    public function getFitmentSummaryAttribute(): string
    {
        if ($this->fitments->isEmpty()) {
            return 'Universal / Lihat deskripsi';
        }

        $grouped = $this->fitments
            ->groupBy(fn ($m) => $m->bikeBrand?->name . ' ' . $m->name);

        return $grouped->map(function ($models, $modelName) {
            $years = $models->pluck('pivot.year')->filter()->sort()->values();
            $yearStr = $years->isEmpty() ? 'All Years' : $years->implode(', ');

            return "{$modelName} ({$yearStr})";
        })->implode(', ');
    }
}
