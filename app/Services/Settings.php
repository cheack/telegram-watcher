<?php

namespace App\Services;

class Settings
{
    protected static ?string $filePath = null;

    protected static array $data = [];

    protected static bool $loaded = false;

    protected static function getFilePath(): string
    {
        if (static::$filePath === null) {
            static::$filePath = base_path('settings.json');
        }
        return static::$filePath;
    }

    protected static function load(): void
    {
        if (static::$loaded) {
            return;
        }

        $file = static::getFilePath();

        if (!file_exists($file)) {
            static::$data = [];
            static::$loaded = true;
            return;
        }

        $contents = file_get_contents($file);
        $decoded = json_decode($contents, true);

        static::$data = is_array($decoded) ? $decoded : [];
        static::$loaded = true;
    }

    protected static function save(): void
    {
        $json = json_encode(static::$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents(static::getFilePath(), $json);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        static::load();

        $keys = explode('.', $key);
        $value = static::$data;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        static::load();

        $keys = explode('.', $key);
        $dataRef = &static::$data;

        foreach ($keys as $segment) {
            if (!isset($dataRef[$segment]) || !is_array($dataRef[$segment])) {
                $dataRef[$segment] = [];
            }
            $dataRef = &$dataRef[$segment];
        }

        $dataRef = $value;

        static::save();
    }

    public static function forget(string $key): void
    {
        static::load();

        $keys = explode('.', $key);
        $dataRef = &static::$data;

        while (count($keys) > 1) {
            $segment = array_shift($keys);
            if (!isset($dataRef[$segment]) || !is_array($dataRef[$segment])) {
                return;
            }
            $dataRef = &$dataRef[$segment];
        }

        $lastKey = array_shift($keys);
        if (isset($dataRef[$lastKey])) {
            unset($dataRef[$lastKey]);
            static::save();
        }
    }

    public static function has(string $key): bool
    {
        static::load();

        $keys = explode('.', $key);
        $value = static::$data;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return false;
            }
            $value = $value[$segment];
        }

        return true;
    }
}
