<?php
namespace App\Services;

class Settings
{
    protected static string $prefix = 'settings.';

    public static function get(string $key, mixed $default = null): mixed
    {
        return cache()->get(static::$prefix . $key, $default);
    }

    public static function set(string $key, mixed $value): void
    {
        cache()->forever(static::$prefix . $key, $value);
    }

    public static function unset(string $key): void
    {
        cache()->forget(static::$prefix . $key);
    }

    public static function has(string $key): bool
    {
        return cache()->has(static::$prefix . $key);
    }
}
