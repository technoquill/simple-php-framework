<?php
declare(strict_types=1);

use Technoquill\Framework\App;

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = $_SERVER[$key] ?? $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        // The next is auto-typing:
        $lower = strtolower($value);

        // Explicit true/false
        if ($lower === 'true' || $lower === '(true)') {
            return true;
        }
        if ($lower === 'false' || $lower === '(false)') {
            return false;
        }
        // Explicit null
        if ($lower === 'null' || $lower === '(null)') {
            return null;
        }
        // Explicit empty
        if ($lower === 'empty' || $lower === '(empty)') {
            return '';
        }
        // Numeric|Float
        if (is_numeric($value)) {
            return $value + 0;
        }

        // Leave as is (string)
        return $value;
    }
}

if(!function_exists('app_version')) {
    function app_version(): string
    {
        return App::VERSION;
    }
}


if(!function_exists('base_path')) {
    function base_path(): string
    {
        return APP_BASE_PATH;
    }
}