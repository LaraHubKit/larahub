<?php

/**
 * LaraHub Helper Functions
 * Laravel-style env() and config()
 */

if (!function_exists('env')) {
    /**
     * Get an environment variable (Laravel-style)
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === '') {
            return $default;
        }
        return match (strtolower($value)) {
            'true', '(true)'  => true,
            'false', '(false)' => false,
            'null', '(null)'  => null,
            'empty', '(empty)' => '',
            default           => $value,
        };
    }
}

if (!function_exists('config')) {
    /**
     * Get a config value (Laravel-style, dot notation)
     *
     * @param  string|null  $key   e.g. 'filesystem.default', 'database'
     * @param  mixed        $default
     * @return mixed
     */
    function config(?string $key = null, mixed $default = null): mixed
    {
        static $configCache = [];
        $basePath = dirname(__DIR__);
        $configPath = $basePath . '/config';

        if ($key === null) {
            return $configCache;
        }

        $parts = explode('.', $key, 2);
        $file = $parts[0];
        $subKey = $parts[1] ?? null;

        if (!isset($configCache[$file])) {
            $path = $configPath . '/' . $file . '.php';
            $configCache[$file] = file_exists($path) ? require $path : [];
        }

        $value = $configCache[$file];

        if ($subKey === null) {
            return $value ?: $default;
        }

        foreach (explode('.', $subKey) as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }
}

/**
 * Template helpers for .hub.php views (description.txt)
 */
if (!function_exists('asset')) {
    function asset(string $path): string {
        $path = ltrim($path, '/');
        return '/' . $path;
    }
}

if (!function_exists('route')) {
    function route(string $name, array $params = []): string {
        // Placeholder: Router doesn't support named routes yet
        return '#';
    }
}

if (!function_exists('lh_asset')) {
    function lh_asset(string $path): string {
        return asset($path);
    }
}

if (!function_exists('lh_route')) {
    function lh_route(string $name): string {
        return route($name);
    }
}
