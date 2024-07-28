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
use ReflectionProperty;

final class PropertyReflection extends AggregateRoot implements Normalizable, AttributesSupport, ReflectionInterface
{
    use Attributes;

    private ClassReflection $parent;
    private PropertyReflection $parentProperty;

    private function __construct(
        private ReflectionId $id,
        private string $name,
    ) {
    }

    public static function create(\ReflectionProperty $reflectionProperty, ClassReflection $parent, ?ReflectionProperty $parentProperty): static
    {
        
    }

    public static function recreate(ReflectionId $id): static
    {
        return new static($id);
    }
    
    public function parent(null|ClassReflection $parent = null): ClassReflection
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

    public function parentProperty(null|PropertyReflection $parentProperty = null): PropertyReflection
    {
        if (!$parentProperty) {
            return $this->parentProperty;
        }

        if (!property_exists($this, 'parentProperty')) {
            $this->parentProperty = $parentProperty;

            return $this->parentProperty;
        }

        throw new \InvalidArgumentException("Cannot set parentProperty property. parentProperty property is read-only.");
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
