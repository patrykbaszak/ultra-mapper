<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Model\Assets;

use PBaszak\UltraMapper\Blueprint\Application\Exception\BlueprintException;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Type;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

class ParameterBlueprint implements Normalizable
{
    public MethodBlueprint $parent;
    public string $name;

    public Type $type;
    public bool $hasDefaultValue;
    public mixed $defaultValue;

    public static function create(\ReflectionParameter $parameter, MethodBlueprint $parent): self
    {
        $instance = new self();
        $instance->parent = $parent;
        $instance->name = $parameter->getName();
        $instance->type = Type::create($instance);
        if ($instance->hasDefaultValue = $parameter->isOptional()) {
            $instance->defaultValue = $parameter->getDefaultValue();
        } else {
            $instance->defaultValue = null;
        }

        return $instance;
    }

    public static function createCollection(MethodBlueprint $parent): AssetsAggregate
    {
        $ref = $parent->getReflection();

        $parameters = [];
        foreach ($ref->getParameters() as $parameter) {
            $parameters[$parameter->getName()] = static::create($parameter, $parent);
        }

        return new AssetsAggregate($parent, $parameters);
    }

    public function getReflection(): \ReflectionParameter
    {
        $parameters = Blueprint::getClassBlueprint($this)->getReflection()->getMethod($this->parent->name)->getParameters();
        foreach ($parameters as $parameter) {
            if ($parameter->getName() === $this->name) {
                return $parameter;
            }
        }

        throw new BlueprintException('Parameter not found in the reflection method.', 5923);
    }

    public function normalize(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type->normalize(),
            'hasDefaultValue' => $this->hasDefaultValue,
            'defaultValue' => $this->defaultValue,
        ];
    }

    public function __clone(): void
    {
        $this->type = clone $this->type;
        $this->type->parent = $this;
    }
}
