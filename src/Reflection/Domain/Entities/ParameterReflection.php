<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\AttributesSupport;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\ReflectionInterface;
use PBaszak\UltraMapper\Reflection\Domain\Entities\traits\Attributes;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionCreated;
use PBaszak\UltraMapper\Reflection\Domain\Identity\ReflectionId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\AggregateRoot;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

final class ParameterReflection extends AggregateRoot implements Normalizable, AttributesSupport, ReflectionInterface
{
    use Attributes;

    private MethodReflection $parent;

    private function __construct(
        private ReflectionId $id,
        private string $name,
        private TypeReflection $type,
        array $attributes,
    ) {
        $this->attributes = $attributes;
    }

    public static function create(\ReflectionParameter $reflectionMethod, MethodReflection $parent): static
    {
        $instance = new static(
            id: ReflectionId::uuid(),
            name: $reflectionMethod->getName(),
            attributes: [],
            parameters: [],
        );

        $instance->parent = $parent;
        $instance->raise(
            new ReflectionCreated($instance->id)
        );

        return $instance;
    }

    public static function recreate(ReflectionId $id): static
    {
        return new static($id);
    }
    
    public function parent(null|MethodReflection $parent = null): MethodReflection
    {
        if (!$parent) {
            return $this->parent;
        }

        if (!property_exists($this, 'parent')) {
            $this->parent = $parent;

            return $this->parent;
        }

        throw new \InvalidArgumentException("Cannot set parent property. Parent property is read-only.");
    }

    public function id(): ReflectionId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
