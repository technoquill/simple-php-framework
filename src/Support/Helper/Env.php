<?php
declare(strict_types=1);

namespace Technoquill\Framework\Support\Helper;

final class Env
{
    /**
     * Retrieves the value of an environment variable using the provided key.
     * If the key does not exist, returns the provided default value.
     *
     * @param string $key The name of the environment variable to retrieve.
     * @param mixed $default The default value to return if the environment variable does not exist.
     * @return mixed The value of the environment variable or the default value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return env($key, $default);
    }

}