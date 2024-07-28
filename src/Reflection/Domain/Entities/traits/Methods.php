<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities\traits;

use PBaszak\UltraMapper\Reflection\Domain\Entities\MethodReflection;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionAdded;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionRemoved;

trait Methods
{
    /**
     * @var array<string, MethodReflection>
     */
    private array $methods = [];

    /**
     * @param null|string $name
     * 
     * @return array<string, MethodReflection>|MethodReflection
     */
    public function methods(null|string $name = null): array|MethodReflection
    {
        if ($name) {
            if (!array_key_exists($name, $this->methods)) {
                throw new \InvalidArgumentException("Method `$name` does not exist in $this->shortName class.");
            }

            return $this->methods[$name];
        }

        return $this->methods;
    }

    public function addMethod(MethodReflection $method): void
    {
        $this->methods[$method->name()] = $method;
        $this->raise(
            new ReflectionAdded($method->id(), ReflectionAdded::METHOD_REFLECTION_ADDED)
        );
    }

    public function removeMethod(string $name): void
    {
        if (!array_key_exists($name, $this->methods)) {
            throw new \InvalidArgumentException("Method `$name` does not exist in $this->shortName class.");
        }
        $id = $this->methods($name)->id();
        unset($this->methods[$name]);

        $this->raise(
            new ReflectionRemoved($id, ReflectionRemoved::METHOD_REFLECTION_REMOVED)
        );
    }

    private function normalizeMethods(): array
    {
        return array_map(
            fn (MethodReflection $item): array => $item->normalize(),
            $this->methods
        );
    }

    public static function denormalizeMethods(array $methods): array
    {
        return array_map(
            fn (array $item): MethodReflection => MethodReflection::denormalize($item),
            $methods
        );
    }
}
