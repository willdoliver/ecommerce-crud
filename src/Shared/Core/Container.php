<?php

namespace App\Shared\Core;

use ReflectionClass;
use ReflectionParameter;

class Container
{
    private array $bindings = [];

    /**
     * Associa uma interface a uma implementação concreta.
     */
    public function bind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Resolve e constrói uma instância de uma classe.
     *
     * @throws \Exception
     */
    public function resolve(string $class, array $extraParams = []): object
    {
        // Se a classe for uma interface, encontra sua implementação concreta
        $class = $this->bindings[$class] ?? $class;

        $reflector = new ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("A classe [$class] não é instanciável.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $class; // Não tem construtor, apenas instancia
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            // Se um parâmetro extra com o mesmo nome foi passado, usa ele
            if (array_key_exists($parameter->getName(), $extraParams)) {
                $dependencies[] = $extraParams[$parameter->getName()];
                continue;
            }

            $type = $parameter->getType();
            // Resolve a dependência recursivamente
            $dependencies[] = $this->resolve($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
