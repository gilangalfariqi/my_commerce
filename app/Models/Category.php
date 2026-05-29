<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order', 'asc');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function getUrlAttribute(): string
    {
        if (!$this->image) {
            return '';
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Industry-standard deterministic ordering for catalogs.
     * Keeps the UI stable: primary by sort_order, then by name.
     */
    public function scopeOrdered(
        \Illuminate\Database\Eloquent\Builder $query
    ) {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }
}

