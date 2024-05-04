<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

/**
 * The representation of a class or a property attribute.
 */
class Attribute
{
    public Blueprint|Property $parent;

    public static function create(\ReflectionAttribute $attribute, Blueprint|Property $parent): self
    {
        $instance = new self();
        $instance->parent = $parent;

        return $instance;
    }

    public function isPropertyAttribute(): bool
    {
        return $this->parent instanceof Property;
    }

    public function isBlueprintAttribute(): bool
    {
        return $this->parent instanceof Blueprint;
    }
}
