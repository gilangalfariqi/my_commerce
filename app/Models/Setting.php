<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    public static function getValue(string $key, $default = null): ?string
    {
        return Cache::remember("setting_{$key}", 86400, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function setValue(string $key, ?string $value, ?string $description = null): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        if ($description) {
            $setting->description = $description;
            $setting->save();
        }

        Cache::forget("setting_{$key}");

        return $setting;
    }
}
