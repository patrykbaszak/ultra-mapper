<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\AttributesSupport;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\ReflectionInterface;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Traits\Attributes;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Traits\Parameters;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\TypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionCreated;
use PBaszak\UltraMapper\Reflection\Domain\Factories\TypeReflectionFactory;
use PBaszak\UltraMapper\Reflection\Domain\Identity\ReflectionId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\Entity;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

final class MethodReflection extends Entity implements Normalizable, AttributesSupport, ReflectionInterface
{
    use Attributes;
    use Parameters;

    private ClassReflection $parent;

    private function __construct(
        private ReflectionId $id,
        private string $name,
        array $attributes,
        array $parameters,
        private TypeReflection $returnType,
    ) {
        $this->attributes = $attributes;
        $this->parameters = $parameters;
    }

    public static function create(\ReflectionMethod $reflectionMethod, ClassReflection $parent): static
    {
        $instance = new static(
            id: ReflectionId::uuid(),
            name: $reflectionMethod->getName(),
            attributes: [],
            parameters: [],
            returnType: (new TypeReflectionFactory())->createForMethod($reflectionMethod),
        );

        $instance->parent = $parent;
        $instance->raise(
            new ReflectionCreated($instance->id)
        );

        foreach ($reflectionMethod->getAttributes() as $attribute) {
            $instance->addAttribute(AttributeReflection::create($attribute, $instance));
        }

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $instance->addParameter(ParameterReflection::create($parameter, $instance));
        }

        return $instance;
    }

    /**
     * @param array<class-string, AttributeReflection[]> $attributes
     * @param array<string, ParameterReflection>         $parameters
     */
    public static function recreate(
        ReflectionId $id,
        string $name,
        array $attributes,
        array $parameters,
        TypeReflection $returnType,
    ): static {
        return new static(
            $id,
            $name,
            $attributes,
            $parameters,
            $returnType,
        );
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

    public function id(): ReflectionId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function returnType(): TypeReflection
    {
        return $this->returnType;
    }

    public function reflection(): \Reflector
    {
        /** @var \ReflectionClass */
        $parentReflection = $this->parent->reflection();

        return $parentReflection->getMethod($this->name);
    }

    public function normalize(): array
    {
        return [
            'id' => $this->id->value,
            'name' => $this->name,
            'attributes' => $this->normalizeAttributes(),
            'parameters' => $this->normalizeParameters(),
            'returnType' => $this->returnType->normalize(),
        ];
    }

    public static function denormalize(array $data): static
    {
        $instance = static::recreate(
            ReflectionId::recreate($data['id']),
            $data['name'],
            self::denormalizeAttributes($data['attributes']),
            self::denormalizeParameters($data['parameters']),
            $data['returnType']['type']::denormalize($data['returnType']),
        );

        foreach ($instance->attributes() as $attrs) {
            foreach ($attrs as $attr) {
                $attr->parent($instance);
            }
        }

        foreach ($instance->parameters() as $param) {
            $param->parent($instance);
        }

        return $instance;
    }
}
