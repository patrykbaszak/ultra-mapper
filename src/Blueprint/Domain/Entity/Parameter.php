<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Domain\Accessor\Accessor;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\BlueprintException;
use PBaszak\UltraMapper\Blueprint\Domain\Normalizer\Normalizable;

/**
 * The representation of the method parameter.
 */
class Parameter implements Normalizable
{
    public Method $parent;
    public string $name;

    public Type $type;
    public bool $hasDefaultValue;
    public mixed $defaultValue;

    public static function create(\ReflectionParameter $parameter, Method $parent): self
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

    public function getReflection(): \ReflectionParameter
    {
        $parameters = (new Accessor($this->parent))->getBlueprint()->getReflection()->getMethod($this->parent->name)->getParameters();
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
}
