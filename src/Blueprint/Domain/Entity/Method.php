<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

/**
 * The representation of the class method.
 */
class Method
{
    public Blueprint $parent;

    public static function create(\ReflectionMethod $method, Blueprint $parent): self
    {
        $instance = new self();
        $instance->parent = $parent;

        return $instance;
    }
}
