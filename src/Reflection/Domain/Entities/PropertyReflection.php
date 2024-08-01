<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\AttributesSupport;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\ReflectionInterface;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Traits\Attributes;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\TypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionCreated;
use PBaszak\UltraMapper\Reflection\Domain\Factories\TypeReflectionFactory;
use PBaszak\UltraMapper\Reflection\Domain\Identity\ReflectionId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\Entity;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

final class PropertyReflection extends Entity implements Normalizable, AttributesSupport, ReflectionInterface
{
    use Attributes;

    private ClassReflection $parent;
    private PropertyReflection $parentProperty;

    private function __construct(
        private ReflectionId $id,
        private string $name,
        private TypeReflection $type,
        array $attributes = [],
    ) {
        $this->attributes = $attributes;
    }

    public static function create(\ReflectionProperty $reflectionProperty, ClassReflection $parent, ?\ReflectionProperty $parentProperty): static
    {
        $instance = new static(
            id: ReflectionId::uuid(),
            name: $reflectionProperty->getName(),
            type: (new TypeReflectionFactory())->createForProperty($reflectionProperty),
        );

        $instance->parent = $parent;
        $instance->parentProperty = $parentProperty;
        $instance->raise(
            new ReflectionCreated($instance->id)
        );

        foreach ($reflectionProperty->getAttributes() as $attribute) {
            $instance->addAttribute(AttributeReflection::create($attribute, $instance));
        }

        return $instance;
    }

    /**
     * @param array<class-string, AttributeReflection[]> $attributes
     */
    public static function recreate(
        ReflectionId $id,
        string $name,
        TypeReflection $type,
        array $attributes,
    ): static {
        return new static(
            $id,
            $name,
            $type,
            $attributes,
        );
    }

    public function reflection(): \Reflector
    {
        return new \ReflectionProperty($this->parent->name(), $this->name);
    }

    public function parent(?ClassReflection $parent = null): ClassReflection
    {
        if (!$parent) {
            return $this->parent;
        }

        if (!property_exists($this, 'parent')) {
            $this->parent = $parent;

            return $this->parent;
        }

        throw new \InvalidArgumentException('Cannot set parent property. Parent property is read-only.');
    }

    public function parentProperty(?PropertyReflection $parentProperty = null): PropertyReflection
    {
        if (!$parentProperty) {
            return $this->parentProperty;
        }

        if (!property_exists($this, 'parentProperty')) {
            $this->parentProperty = $parentProperty;

            return $this->parentProperty;
        }

        throw new \InvalidArgumentException('Cannot set parentProperty property. parentProperty property is read-only.');
    }

    public function id(): ReflectionId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): TypeReflection
    {
        return $this->type;
    }

    public function normalize(): array
    {
        return [
            'id' => $this->id->value,
            'name' => $this->name,
            'type' => $this->type->normalize(),
            'attributes' => $this->normalizeAttributes(),
        ];
    }

    public static function denormalize(array $data): static
    {
        $instance = static::recreate(
            ReflectionId::recreate($data['id']),
            $data['name'],
            $data['type']['type']::denormalize($data['type']),
            static::denormalizeAttributes($data['attributes']),
        );

        foreach ($instance->attributes() as $attrs) {
            foreach ($attrs as $attr) {
                $attr->parent($instance);
            }
        }

        return $instance;
    }
}
