<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasUuids;

    protected $guarded = [];

    public const CACHE_KEY = 'hms.settings.all';

    protected static function booted(): void
    {
        // Bust the settings cache whenever a setting changes.
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * Return the value cast to its declared type.
     */
    public function getCastedValueAttribute(): mixed
    {
        return static::castValue($this->value, $this->type);
    }

    public static function castValue(mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => (string) $value,
        };
    }

    /**
     * All settings keyed by their dot key, cached for the request lifecycle.
     *
     * @return array<string, mixed>
     */
    public static function allKeyed(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::all()
                ->mapWithKeys(fn (Setting $s) => [$s->key => static::castValue($s->value, $s->type)])
                ->toArray();
        });
    }
}
