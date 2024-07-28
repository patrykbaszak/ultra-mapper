<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities\traits;

use PBaszak\UltraMapper\Reflection\Domain\Entities\PropertyReflection;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionAdded;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionRemoved;

trait Properties
{
    /**
     * @var array<string, PropertyReflection>
     */
    private array $properties = [];

    /**
     * @param null|string $name
     * 
     * @return array<string, PropertyReflection>|PropertyReflection
     */
    public function properties(null|string $name = null): array|PropertyReflection
    {
        if ($name) {
            if (!array_key_exists($name, $this->properties)) {
                throw new \InvalidArgumentException("Property `$name` does not exist in $this->shortName class.");
            }

            return $this->properties[$name];
        }

        return $this->properties;
    }

    public function addProperty(PropertyReflection $property): void
    {
        $this->properties[$property->name()] = $property;
        $this->raise(
            new ReflectionAdded($property->id(), ReflectionAdded::PROPERTY_REFLECTION_ADDED)
        );
    }

    public function removeProperty(string $name): void
    {
        if (!array_key_exists($name, $this->properties)) {
            throw new \InvalidArgumentException("Property `$name` does not exist in $this->shortName class.");
        }
        $id = $this->properties($name)->id();
        unset($this->properties[$name]);

        $this->raise(
            new ReflectionRemoved($id, ReflectionRemoved::PROPERTY_REFLECTION_REMOVED)
        );
    }

    private function normalizeProperties(): array
    {
        return array_map(
            fn (PropertyReflection $item): array => $item->normalize(),
            $this->properties
        );
    }

    public static function denormalizeProperties(array $properties): array
    {
        return array_map(
            fn (array $item): PropertyReflection => PropertyReflection::denormalize($item),
            $properties
        );
    }
}
