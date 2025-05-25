<?php
declare(strict_types=1);

namespace Technoquill\Framework\Support\Resolver;


use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionFunctionAbstract;
use RuntimeException;

final class CallableResolver
{
    /** @var object  */
    private object $container;

    public function __construct(object $container)
    {
        $this->container = $container;
    }

    /**
     * Викликає будь-який callable/method з автозаповненням DI-залежностей та параметрів з $params.
     *
     * @param callable|array $handler Closure, [Class, 'method'], або будь-який інший callable.
     * @param array $params Route params чи довільний масив значень для підстановки по іменах.
     * @return mixed                  Результат виконання callable.
     * @throws ReflectionException
     */
    public function call(callable|array $handler, array $params = []): mixed
    {
        // [Class, 'method']
        if (is_array($handler) && is_string($handler[0]) && is_string($handler[1])) {
            $object = $this->container->get($handler[0]);
            $method = $handler[1];
            $reflection = new ReflectionMethod($object, $method);
        }  else {
            // Closure or function
            $reflection = new ReflectionFunction($handler(...));
            $object = null;
        }
        $args = $this->resolveParameters($reflection, $params);
        return $object
            ? $reflection->invokeArgs($object, $args)
            : $reflection->invokeArgs($args);
    }

    /**
     * Підбирає аргументи для callable згідно з DI та переданими параметрами.
     */
    private function resolveParameters(ReflectionFunctionAbstract $reflection, array $params): array
    {
        $args = [];
        foreach ($reflection->getParameters() as $param) {
            $type = $param->getType();
            // DI for Classes
            if ($type && !$type->isBuiltin()) {
                $args[] = $this->container->get($type->getName());
            }
            // route params or outside data
            elseif (array_key_exists($param->getName(), $params)) {
                $args[] = $params[$param->getName()];
            }
            // default values
            elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            }
            else {
                throw new RuntimeException(
                    "Cannot resolve parameter \${$param->getName()}"
                );
            }
        }
        return $args;
    }
}