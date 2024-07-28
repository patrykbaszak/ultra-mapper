<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\AttributesSupport;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\ReflectionInterface;
use PBaszak\UltraMapper\Reflection\Domain\Entities\traits\Attributes;
use PBaszak\UltraMapper\Reflection\Domain\Entities\traits\Parameters;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionCreated;
use PBaszak\UltraMapper\Reflection\Domain\Identity\ReflectionId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\AggregateRoot;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;
use Reflector;

final class MethodReflection extends AggregateRoot implements Normalizable, AttributesSupport, ReflectionInterface
{
    use Attributes, Parameters;

    private ClassReflection $parent;

    private function __construct(
        private ReflectionId $id,
        private string $name,
        private string $docBlock,
        array $attributes,
        array $parameters,
    ) {
        $this->attributes = $attributes;
        $this->parameters = $parameters;
    }

    public static function create(\ReflectionMethod $reflectionMethod, ClassReflection $parent): static
    {
        $instance = new static(
            id: ReflectionId::uuid(),
            name: $reflectionMethod->getName(),
            docBlock: $reflectionMethod->getDocComment(),
            attributes: [],
            parameters: [],
        );

        $instance->parent = $parent;
        $instance->raise(
            new ReflectionCreated($instance->id)
        );

        return $instance;
    }

    /**
     * @param ReflectionId $id
     * @param string $name
     * @param string $docBlock
     * @param array $attributes
     * @param array $parameters
     */
    public static function recreate(
        ReflectionId $id,
        string $name,
        string $docBlock,
        array $attributes,
        array $parameters,
    ): static {
        return new static(
            $id,
            $name,
            $docBlock,
            $attributes,
            $parameters,
        );
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

    public function id(): ReflectionId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function docBlock(): string
    {
        return $this->docBlock;
    }

    public function parameters(): array
    {
        return $this->parameters;
    }

    public function reflection(): Reflector
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
            'docBlock' => $this->docBlock,
            'attributes' => $this->normalizeAttributes(),
            'parameters' => $this->normalizeParameters(),
        ];
    }

    public static function denormalize(array $data): static
    {
        $instance = static::recreate(
            ReflectionId::recreate($data['id']),
            $data['name'],
            $data['docBlock'],
            self::denormalizeAttributes($data['attributes']),
            self::denormalizeParameters($data['parameters']),
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
