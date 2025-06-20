<?php

namespace Todo\Core;

use ReflectionClass;
use ReflectionException;

class Container
{
    protected array $bindings = [];

    /**
     * Manuális binding regisztrálása
     */
    public function bind(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Osztály példányosítása, automatikus vagy manuális úton
     */
    public function resolve(string $class)
    {
        // Manuálisan regisztrált binding
        if (isset($this->bindings[$class])) {
            return call_user_func($this->bindings[$class], $this);
        }

        try {
            $reflector = new ReflectionClass($class);

            if (!$reflector->isInstantiable()) {
                throw new \Exception("Class {$class} is not instantiable.");
            }

            $constructor = $reflector->getConstructor();

            if (is_null($constructor)) {
                return new $class;
            }

            $parameters = $constructor->getParameters();
            $dependencies = [];

            foreach ($parameters as $parameter) {
                $type = $parameter->getType();

                if ($type === null) {
                    throw new \Exception("Cannot resolve parameter \${$parameter->getName()} of class {$class}");
                }

                $dependencies[] = $this->resolve($type->getName());
            }

            return $reflector->newInstanceArgs($dependencies);

        } catch (ReflectionException $e) {
            throw new \Exception("Unable to resolve class: {$class}. Error: " . $e->getMessage());
        }
    }

    /**
     * Függőségek manuális regisztrálása
     */
    public function addInjections()
    {
        // Adatbázis kapcsolat
        $this->bind(Connection::class, function () {
            return new Connection();
        });

        // Alap modellek/szolgáltatások
        $this->bind(AuthService::class, function ($container) {
            return new AuthService(
                $container->resolve(Connection::class)
            );
        });

        $this->bind(\Todo\Models\UserModel::class, function ($container) {
            return new \Todo\Models\UserModel(
                $container->resolve(Connection::class)
            );
        });

        $this->bind(\Todo\Models\TasksModel::class, function ($container) {
            return new \Todo\Models\TasksModel(
                $container->resolve(Connection::class)
            );
        });

        // Kontrollerek
        $this->bind(\Todo\Controllers\AdminController::class, function ($container) {
            return new \Todo\Controllers\AdminController(
                $container->resolve(AuthService::class)
            );
        });

        $this->bind(\Todo\Controllers\UserController::class, function ($container) {
            return new \Todo\Controllers\UserController(
                $container->resolve(\Todo\Models\UserModel::class),
                $container->resolve(AuthService::class)
            );
        });

        $this->bind(\Todo\Controllers\TasksController::class, function ($container) {
            return new \Todo\Controllers\TasksController(
                $container->resolve(\Todo\Models\TasksModel::class),
                $container->resolve(\Todo\Models\UserModel::class),
                $container->resolve(AuthService::class)
            );
        });

        $this->bind(\Todo\Controllers\GuestController::class, function ($container) {
            return new \Todo\Controllers\GuestController(
                $container->resolve(\Todo\Models\TasksModel::class),
                $container->resolve(AuthService::class)
            );
        });

        $this->bind(\Todo\Controllers\GuestController::class, function ($container) {
            return new \Todo\Controllers\GuestController(
                $container->resolve(\Todo\Models\TasksModel::class),
                $container->resolve(AuthService::class)
            );
        });
    }
}
