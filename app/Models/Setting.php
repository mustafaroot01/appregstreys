<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Get a setting value by key, with safe JSON caching
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $cached = Cache::get("setting_{$key}");

        if ($cached !== null) {
            $data = json_decode($cached, true);
            return $data['value'] ?? $default;
        }

        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        $value = match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json'    => json_decode($setting->value, true),
            default   => $setting->value,
        };

        Cache::forever("setting_{$key}", json_encode(['value' => $value, 'type' => $setting->type]));

        return $value;
    }

    /**
     * Set a setting value by key
     */
    public static function setValue(string $key, mixed $value): void
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            if (is_bool($value)) $value = $value ? '1' : '0';
            if (is_array($value)) $value = json_encode($value);

            $setting->update(['value' => (string) $value]);
            Cache::forget("setting_{$key}");
        }
    }

    /**
     * Get all settings grouped
     */
    public static function getByGroup(string $group): array
    {
        return self::where('group', $group)
            ->get()
            ->mapWithKeys(fn ($s) => [$s->key => match ($s->type) {
                'boolean' => (bool) $s->value,
                'integer' => (int) $s->value,
                'json'    => json_decode($s->value, true),
                default   => $s->value,
            }])
            ->toArray();
    }
}
