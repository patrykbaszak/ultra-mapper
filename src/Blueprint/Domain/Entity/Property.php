<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Application\Enum\PropertyVisibility;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\AttributeAggregate;

/**
 * The representation of the class property.
 */
class Property
{
    public Blueprint $parent;
    public string $originName;

    public PropertyVisibility $visibility;
    public bool $isStatic;
    public bool $isReadOnly;
    public bool $hasDefaultValue;
    public mixed $defaultValue;
    public false|string $docBlock;

    public AttributeAggregate $attributes;

    public static function create(\ReflectionProperty $property, Blueprint $parent): self
    {
        $instance = new self();
        $instance->parent = $parent;
        $instance->originName = $property->getName();
        $instance->visibility = match (true) {
            $property->isPrivate() => PropertyVisibility::PRIVATE,
            $property->isProtected() => PropertyVisibility::PROTECTED,
            default => PropertyVisibility::PUBLIC,
        };
        $instance->isStatic = $property->isStatic();
        $instance->isReadOnly = $property->isReadOnly();
        $instance->hasDefaultValue = $property->hasDefaultValue();
        $instance->defaultValue = $property->getDefaultValue();

        return $instance;
    }

    public function getReflection(): \ReflectionProperty
    {
        return new \ReflectionProperty($this->parent->name, $this->originName);
    }
}
