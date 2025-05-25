<?php
declare(strict_types=1);

namespace Technoquill\Framework\Context;

use RuntimeException;

abstract class AbstractContext
{

    private static ?self $instance = null;


    public static function setInstance(self $context): void
    {
        static::$instance = $context;
    }

    public static function getInstance(): self
    {
        if (!static::$instance) {
            throw new RuntimeException('No context initialized');
        }
        return static::$instance;
    }

    public static function set(array $array): void
    {
        $instance = new static();
        foreach ($array as $property => $value) {
            $instance->$property = $value;
        }
        static::setInstance($instance);
    }

}