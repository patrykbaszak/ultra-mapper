<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Application\Enum\Visibility;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\AttributeAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Normalizer\Normalizable;

/**
 * The representation of the class property.
 */
class Property implements Normalizable
{
    public Blueprint $parent;
    public string $originName;

    public Visibility $visibility;
    public Type $type;
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
            $property->isPrivate() => Visibility::PRIVATE,
            $property->isProtected() => Visibility::PROTECTED,
            default => Visibility::PUBLIC,
        };
        $instance->type = Type::create($instance);
        $instance->isStatic = $property->isStatic();
        $instance->isReadOnly = $property->isReadOnly();
        $instance->hasDefaultValue = $property->hasDefaultValue();
        $instance->defaultValue = $property->getDefaultValue();
        $instance->docBlock = $property->getDocComment();

        $instance->attributes = AttributeAggregate::create($instance);

        return $instance;
    }

    public function getReflection(): \ReflectionProperty
    {
        return new \ReflectionProperty($this->parent->name, $this->originName);
    }

    public function normalize(): array
    {
        return [
            'originName' => $this->originName,
            'visibility' => $this->visibility->value,
            'type' => $this->type->normalize(),
            'isStatic' => $this->isStatic,
            'isReadOnly' => $this->isReadOnly,
            'hasDefaultValue' => $this->hasDefaultValue,
            'defaultValue' => $this->defaultValue,
            'docBlock' => $this->docBlock,
            'attributes' => $this->attributes->normalize(),
        ];
    }
}
