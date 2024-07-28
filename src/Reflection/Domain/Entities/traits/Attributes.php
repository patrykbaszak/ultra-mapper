<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities\traits;

use PBaszak\UltraMapper\Reflection\Domain\Entities\AttributeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionAdded;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionRemoved;

trait Attributes
{
    /**
     * @var array<class-string, AttributeReflection[]>
     */
    private array $attributes = [];

    /**
     * @param null|class-string $filter
     * 
     * @return array<class-string, AttributeReflection[]>|AttributeReflection[]
     */
    public function attributes(null|string $filter = null): array
    {
        if ($filter) {
            return $this->attributes[$filter] ?? [];
        }

        return $this->attributes;
    }

    public function addAttribute(AttributeReflection $attribute): void
    {
        $this->attributes[$attribute->name()][] = $attribute;
        $this->raise(
            new ReflectionAdded($attribute->id(), ReflectionAdded::ATTRIBUTE_REFLECTION_ADDED)
        );
    }

    public function removeAttribute(AttributeReflection $attribute): void
    {
        $id = $attribute->id();
        $name = $attribute->name();
        if (!array_key_exists($name, $this->attributes)) {
            throw new \InvalidArgumentException("Attribute `$name` does not exist in $this->shortName class.");
        }

        $deleted = false;
        foreach ($this->attributes[$name] as $key => $attr) {
            if ($attr->id() === $id) {
                unset($this->attributes[$name][$key]);
                $deleted = true;
                break;
            }
        }

        if (!$deleted) {
            throw new \InvalidArgumentException("Attribute `$name` with id `$id` does not exist in $this->shortName class.");
        }

        $this->raise(
            new ReflectionRemoved($id, ReflectionRemoved::ATTRIBUTE_REFLECTION_REMOVED)
        );
    }

    private function normalizeAttributes(): array
    {
        return array_map(
            fn (array $attrs): array => array_map(
                fn (AttributeReflection $attr): array => $attr->normalize(),
                $attrs
            ),
            $this->attributes
        );
    }

    public static function denormalizeAttributes(array $attributes): array
    {
        return array_map(
            fn (array $attrs) => array_map(
                fn (array $attrData): AttributeReflection => AttributeReflection::denormalize($attrData),
                $attrs
            ),
            $attributes
        );
    }
}
