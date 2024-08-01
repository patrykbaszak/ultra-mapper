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

final class ParameterReflection extends Entity implements Normalizable, AttributesSupport, ReflectionInterface
{
    use Attributes;

    private MethodReflection $parent;

    private function __construct(
        private ReflectionId $id,
        private string $name,
        private TypeReflection $type,
        array $attributes = [],
    ) {
        $this->attributes = $attributes;
    }

    public static function create(\ReflectionParameter $reflectionParameter, MethodReflection $parent): static
    {
        $instance = new static(
            id: ReflectionId::uuid(),
            name: $reflectionParameter->getName(),
            type: (new TypeReflectionFactory())->createForParameter($reflectionParameter),
        );

        $instance->parent = $parent;
        $instance->raise(
            new ReflectionCreated($instance->id)
        );

        foreach ($reflectionParameter->getAttributes() as $attribute) {
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

    public function parent(?MethodReflection $parent = null): MethodReflection
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

    public function reflection(): \Reflector
    {
        /** @var \ReflectionMethod $ref */
        $ref = $this->parent()->reflection();

        return $ref->getParameters()[$this->name];
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
        $instance = new static(
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
