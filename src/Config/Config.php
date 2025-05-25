<?php

namespace Technoquill\Framework\Config;

use Technoquill\Framework\Contract\ConfigInterface;


/**
 * Represents a configuration handler that adheres to the ConfigInterface.
 */
final class Config implements ConfigInterface
{

    /** @var array */
    private array $config;


    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Retrieves a configuration value based on a dot-separated key.
     *
     * @param string $key The dot-separated key representing the desired configuration value.
     * @param mixed $default The default value to return if the specified key does not exist.
     * @return mixed The value corresponding to the key, or the default value if the key does not exist.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;
        foreach ($keys as $k) {
            if (is_array($value) && array_key_exists($k, $value)) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }
        return $value;
    }
}