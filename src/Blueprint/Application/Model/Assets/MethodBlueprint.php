<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Model\Assets;

use PBaszak\UltraMapper\Blueprint\Application\Enum\Visibility;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

class MethodBlueprint implements Normalizable
{
    public ClassBlueprint $parent;
    public string $name;

    public Visibility $visibility;
    public bool $isConstructor;
    public bool $isStatic;
    public false|string $docBlock;

    public AssetsAggregate $parameters;

    public static function create(\ReflectionMethod $method, ClassBlueprint $parent): self
    {
        $instance = new self();
        $instance->parent = $parent;
        $instance->name = $method->getName();
        $instance->visibility = match (true) {
            $method->isPublic() => Visibility::PUBLIC,
            $method->isProtected() => Visibility::PROTECTED,
            $method->isPrivate() => Visibility::PRIVATE
        };
        $instance->isConstructor = $method->isConstructor();
        $instance->isStatic = $method->isStatic();
        $instance->docBlock = $method->getDocComment() ?: false;
        $instance->parameters = ParameterBlueprint::createCollection($instance);

        return $instance;
    }

    public static function createCollection(ClassBlueprint $parent): AssetsAggregate
    {
        $ref = $parent->getReflection();

        $properties = [];
        foreach ($ref->getMethods() as $method) {
            $properties[$method->getName()] = static::create($method, $parent);
        }

        return new AssetsAggregate($parent, $properties);
    }

    public function getReflection(): \ReflectionMethod
    {
        return new \ReflectionMethod($this->parent->name, $this->name);
    }

    public function normalize(): array
    {
        return [
            'visibility' => $this->visibility->value,
            'isConstructor' => $this->isConstructor,
            'isStatic' => $this->isStatic,
            'docBlock' => $this->docBlock,
            'parameters' => $this->parameters->normalize(),
        ];
    }

    public function __clone(): void
    {
        $this->parameters = clone $this->parameters;
        $this->parameters->root = $this;
        foreach ($this->parameters as $parameter) {
            $parameter->parent = $this;
        }
    }
}
