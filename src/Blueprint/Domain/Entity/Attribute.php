<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Domain\Exception\BlueprintException;
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

    public function getReflection(): \ReflectionAttribute
    {
        $attr = $this->parent->getReflection()->getAttributes($this->class);
        if (0 === count($attr)) {
            throw new BlueprintException(sprintf('Attribute %s not found on %s.', $this->class, $this->parent->getReflection()->getName()), 5921);
        }
        if (1 === count($attr)) {
            return $attr[0];
        }
        foreach ($attr as $a) {
            if ($this->arguments === $a->getArguments()) {
                return $a;
            }
        }

        throw new BlueprintException(sprintf('Attribute %s not found on %s.', $this->class, $this->parent->getReflection()->getName()), 5924);
    }

    public function normalize(): array
    {
        return [
            'class' => $this->class,
            'arguments' => $this->arguments,
        ];
    }
}
