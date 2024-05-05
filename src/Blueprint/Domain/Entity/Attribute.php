<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Domain\Normalizer\Normalizable;

/**
 * The representation of a class or a property attribute.
 */
class Attribute implements Normalizable
{
    public Blueprint|Property $parent;

    /** @var class-string */
    public string $class;
    /** @var array<string|int, mixed> */
    public array $arguments;

    public static function create(\ReflectionAttribute $attribute, Blueprint|Property $parent): self
    {
        $instance = new self();
        $instance->parent = $parent;
        $instance->class = $attribute->getName();
        $instance->arguments = $attribute->getArguments();

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

    public function normalize(): array
    {
        return [
            'class' => $this->class,
            'arguments' => $this->arguments,
        ];
    }
}
