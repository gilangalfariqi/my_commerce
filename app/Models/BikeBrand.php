<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $logo_path
 * @property string|null $country_of_origin
 * @property bool $is_active
 * @property int $sort_order
 */
class BikeBrand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'country_of_origin',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    // ─────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────

    public function bikeModels(): HasMany
    {
        return $this->hasMany(BikeModel::class)->orderBy('sort_order')->orderBy('name');
    }

    // ─────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ─────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────

    /**
     * Returns the full public URL for the brand logo.
     * Supports both stored paths and absolute URLs (e.g., external CDN).
     */
    public function getLogoUrlAttribute(): string
    {
        if (! $this->logo_path) {
            return '';
        }

        if (filter_var($this->logo_path, FILTER_VALIDATE_URL)) {
            return $this->logo_path;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    // ─────────────────────────────────────────
    // Route Model Binding
    // ─────────────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
