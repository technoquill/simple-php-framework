<?php
declare(strict_types=1);

namespace Technoquill\Framework\Container;

use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Technoquill\Framework\Contract\ContainerInterface;

/**
 * A dependency injection container for managing service bindings and instances.
 */
final class Container implements ContainerInterface
{

    /** @var array */
    protected array $bindings = [];

    /** @var array */
    protected array $instances = [];

    /** @var Container|null */
    private static ?self $instance = null;


    /**
     * Binds a service to a concrete implementation.
     *
     * @param string $id
     * @param callable $concrete
     * @return void
     */
    public function set(string $id, callable $concrete): void
    {
        $this->bindings[$id] = $concrete;
    }


    /**
     * Retrieves an instance of the requested service by its identifier.
     *
     * @param string $id The identifier of the service to retrieve.
     * @return mixed The instance of the requested service.
     * @throws RuntimeException If the service does not exist.
     * @throws ReflectionException
     */
    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->bindings[$id])) {
            $instance = ($this->bindings[$id])();
            $this->instances[$id] = $instance;
            return $instance;
        }

        // If not registered, try autowire
        $instance = $this->autowire($id);

        $this->instances[$id] = $instance;
        return $instance;
    }

    /**
     * @throws ReflectionException
     */
    protected function autowire(string $class)
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $class();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            if ($type && !$type->isBuiltin()) {
                $dependencies[] = $this->get($type->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $dependencies[] = $param->getDefaultValue();
            } else {
                throw new RuntimeException("Can't resolve parameter {$param->getName()} for $class");
            }
        }
        return $reflection->newInstanceArgs($dependencies);
    }


    /**
     * Sets the instance of the container.
     *
     * @param self $container The container instance to set.
     * @return void
     */
    public static function setInstance(self $container): void
    {
        self::$instance = $container;
    }

    /**
     * Retrieves the singleton instance of the class.
     *
     * @return self The singleton instance of the class.
     * @throws RuntimeException If the singleton instance is not set.
     */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            throw new RuntimeException("Container instance is not set.");
        }
        return self::$instance;
    }


}