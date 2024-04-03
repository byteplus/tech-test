<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class LookupCacheService
{
    public static function generateCacheKey(
        string $type, ?string $id, ?string $username
    ): string
    {
        return "lookup_{$type}_{$id}_{$username}";
    }

    public static function get(string $cacheKey)
    {
        return Cache::get($cacheKey);
    }

    public static function put(string $cacheKey, $data, $minutes = 90)
    {
        Cache::put($cacheKey, $data, now()->addMinutes($minutes));
    }

    public static function has(string $cacheKey)
    {
        return Cache::has($cacheKey);
    }
}
