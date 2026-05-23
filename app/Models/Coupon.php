<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'start_time',
        'end_time',
        'min_spend',
        'max_discount',
        'usage_limit',
        'usage_count',
        'is_active',
    ];

    protected $casts = [
        'value' => 'float',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'min_spend' => 'float',
        'max_discount' => 'float',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function isValidForAmount(float $amount): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->start_time && $this->start_time->gt($now)) {
            return false;
        }

        if ($this->end_time && $this->end_time->lt($now)) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        if ($amount < $this->min_spend) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if (!$this->isValidForAmount($amount)) {
            return 0.0;
        }

        if ($this->type === 'fixed') {
            $discount = $this->value;
        } else {
            // percentage
            $discount = $amount * ($this->value / 100);
        }

        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return min($discount, $amount);
    }
}
