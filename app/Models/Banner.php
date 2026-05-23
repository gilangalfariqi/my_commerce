<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'click_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Scope: only active banners
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getUrlAttribute(): string
    {
        if (!$this->image_path) {
            return '';
        }

        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image_path);
    }
}
