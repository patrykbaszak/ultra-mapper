<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Model\Assets;

use PBaszak\UltraMapper\Blueprint\Application\Enum\Visibility;
use PBaszak\UltraMapper\Blueprint\Application\Model\Type;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

class PropertyBlueprint implements Normalizable
{
    public ClassBlueprint $parent;
    public string $originName;

    public Visibility $visibility;
    public Type $type;
    public bool $isStatic;
    public bool $isReadOnly;
    public bool $hasDefaultValue;
    public mixed $defaultValue;
    public false|string $docBlock;

    public AssetsAggregate $attributes;

    public static function create(\ReflectionProperty $property, ClassBlueprint $parent): self
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

        $instance->attributes = AttributeBlueprint::createCollection($instance);

        return $instance;
    }

    public static function createCollection(ClassBlueprint $root): AssetsAggregate
    {
        $ref = $root->getReflection();

        $properties = [];
        foreach ($ref->getProperties() as $property) {
            $properties[$property->getName()] = static::create($property, $root);
        }

        return new AssetsAggregate($root, $properties);
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

    public function __clone(): void
    {
        $this->type = clone $this->type;
        $this->type->parent = $this;

        $this->attributes = clone $this->attributes;
        $this->attributes->root = $this;
        foreach ($this->attributes as $attribute) {
            $attribute->parent = $this;
        }
    }
}
