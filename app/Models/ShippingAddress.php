<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'district',
        'address_line',
        'postal_code',
        'is_default',
    ];

    protected $casts = [
        'province_id' => 'integer',
        'city_id' => 'integer',
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->last_name ? "{$this->first_name} {$this->last_name}" : $this->first_name;
    }
}
