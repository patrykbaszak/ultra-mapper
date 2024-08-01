<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities\Traits;

use PBaszak\UltraMapper\Reflection\Domain\Entities\ParameterReflection;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionAdded;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionRemoved;

trait Parameters
{
    /**
     * @var array<string, ParameterReflection>
     */
    private array $parameters = [];

    /**
     * @return array<string, ParameterReflection>|ParameterReflection
     */
    public function parameters(?string $name = null): array|ParameterReflection
    {
        if ($name) {
            if (!array_key_exists($name, $this->parameters)) {
                throw new \InvalidArgumentException("Parameter `$name` does not exist in $this->shortName class.");
            }

            return $this->parameters[$name];
        }

        return $this->parameters;
    }

    public function addParameter(ParameterReflection $parameter): void
    {
        $this->parameters[$parameter->name()] = $parameter;
        $this->raise(
            new ReflectionAdded($parameter->id(), ReflectionAdded::PARAMETER_REFLECTION_ADDED)
        );
    }

    public function removeParameter(string $name): void
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new \InvalidArgumentException("Parameter `$name` does not exist in $this->shortName class.");
        }
        $id = $this->parameters($name)->id();
        unset($this->parameters[$name]);

        $this->raise(
            new ReflectionRemoved($id, ReflectionRemoved::PARAMETER_REFLECTION_REMOVED)
        );
    }

    private function normalizeParameters(): array
    {
        return array_map(
            fn (ParameterReflection $item): array => $item->normalize(),
            $this->parameters
        );
    }

    public static function denormalizeParameters(array $parameters): array
    {
        return array_map(
            fn (array $item): ParameterReflection => ParameterReflection::denormalize($item),
            $parameters
        );
    }
}
