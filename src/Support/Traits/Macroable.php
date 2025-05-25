<?php
declare(strict_types=1);

namespace Technoquill\Framework\Support\Traits;

use BadMethodCallException;


/**
 * Provides a mechanism to dynamically add macros (custom methods) to a class.
 *
 * This trait allows static and instance methods to be added dynamically at runtime.
 * Macros are registered by name and associated with a callable implementation.
 * The trait handles calls to undefined methods and associates them with registered macros, if available.
 */
trait Macroable
{

    /** @var array  */
    protected static array $macros = [];


    /**
     * Registers a custom macro with the given name and callable.
     *
     * @param string $name The name of the macro being registered.
     * @param callable $macro The implementation of the macro as a callable.
     * @return void
     */
    public static function macro(string $name, callable $macro): void
    {
        static::$macros[$name] = $macro;
    }

    /**
     * Checks if a macro with the given name exists.
     *
     * @param string $name The name of the macro to check.
     * @return bool Returns true if the macro exists, otherwise false.
     */
    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    /**
     * Dynamically handles static method calls at runtime.
     *
     * @param string $method The name of the static method being called.
     * @param array $parameters The array of parameters passed to the method call.
     * @return mixed The result of the dynamically invoked method if it exists.
     * @throws BadMethodCallException If the called method does not exist.
     */
    public static function __callStatic(string $method, array $parameters)
    {
        if (static::hasMacro($method)) {
            return call_user_func_array(static::$macros[$method], $parameters);
        }
        throw new BadMethodCallException("Method {$method} does not exist.");
    }

    /**
     * Handle dynamic method calls into the class.
     *
     * @param string $method The name of the method being called.
     * @param array $parameters The parameters passed to the method call.
     *
     * @return mixed The result of the macro function if it exists.
     * @throws BadMethodCallException If the method does not exist.
     *
     */
    public function __call(string $method, array $parameters)
    {
        if (static::hasMacro($method)) {
            return call_user_func_array(static::$macros[$method]->bindTo($this, static::class), $parameters);
        }
        throw new BadMethodCallException("Method {$method} does not exist.");
    }

}