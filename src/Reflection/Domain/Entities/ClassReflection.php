<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\AttributesSupport;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\ReflectionInterface;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Traits\Attributes;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Traits\Methods;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Traits\Properties;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionCreated;
use PBaszak\UltraMapper\Reflection\Domain\Exception\ReflectionException;
use PBaszak\UltraMapper\Reflection\Domain\Identity\ReflectionId;
use PBaszak\UltraMapper\Reflection\Domain\Reflection;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\Entity;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

final class ClassReflection extends Entity implements Normalizable, AttributesSupport, ReflectionInterface
{
    use Attributes;
    use Methods;
    use Properties;

    private Reflection $root;

    private function __construct(
        private ReflectionId $id,
        /** @var class-string|string */
        private string $name,
        private string $shortName,
        private string $namespace,
        private string $hash,
        private false|string $fileName,
        private false|string $fileHash,
        array $attributes = [],
        array $properties = [],
        array $methods = [],
    ) {
        $this->attributes = $attributes;
        $this->properties = $properties;
        $this->methods = $methods;
    }

    /**
     * @param string|class-string $name
     */
    public static function create(
        string $name,
        Reflection $root,
        ?PropertyReflection $parentProperty = null,
    ): static {
        if (__CLASS__ === $name) {
            throw new ReflectionException("Cannot create instance of $name class.", "Please do not use `ClassReflection::create` method to create instance of $name class.", 1);
        }

        try {
            $reflection = new \ReflectionClass($name);
        } catch (\ReflectionException $e) {
            $message = $e->getMessage();
            throw new ReflectionException("Class `$name` not found. $message", 'Check if the class exists, has correct namespace and filename, and is properly autoloaded.', 2);
        }

        $instance = new static(
            id: ReflectionId::uuid(),
            name: $name,
            shortName: $reflection->getShortName(),
            namespace: $reflection->getNamespaceName(),
            hash: md5($reflection->__toString()),
            fileName: $reflection->getFileName(),
            fileHash: $reflection->getFileName() ? md5_file($reflection->getFileName()) : false,
        );

        $instance->root = $root;
        $instance->raise(
            new ReflectionCreated($instance->id())
        );

        if (true === $result = $root->addClassReflection($instance)) {
            foreach ($reflection->getAttributes() as $attribute) {
                AttributeReflection::create($attribute, $instance);
            }

            foreach ($reflection->getProperties() as $property) {
                PropertyReflection::create($property, $instance, $parentProperty);
            }

            foreach ($reflection->getMethods() as $method) {
                MethodReflection::create($method, $instance);
            }

            return $instance;
        }

        return $result;
    }

    /**
     * @param string|class-string                        $name
     * @param array<class-string, AttributeReflection[]> $attributes
     * @param array<string, PropertyReflection>          $properties
     * @param array<string, MethodReflection>            $methods
     */
    public static function recreate(
        ReflectionId $id,
        string $name,
        string $shortName,
        string $namespace,
        string $hash,
        false|string $fileName,
        false|string $fileHash,
        array $attributes,
        array $properties,
        array $methods
    ): static {
        return new static(
            $id,
            $name,
            $shortName,
            $namespace,
            $hash,
            $fileName,
            $fileHash,
            $attributes,
            $properties,
            $methods
        );
    }

    public function root(?Reflection $root = null): Reflection
    {
        if (!$root) {
            return $this->root;
        }

        if (!property_exists($this, 'root')) {
            $this->root = $root;

            return $this->root;
        }

        throw new \InvalidArgumentException('Cannot set root property. Root property is read-only.');
    }

    public function id(): ReflectionId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function shortName(): string
    {
        return $this->shortName;
    }

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function hash(): string
    {
        return $this->hash;
    }

    public function fileName(): false|string
    {
        return $this->fileName;
    }

    public function fileHash(): false|string
    {
        return $this->fileHash;
    }

    public function reflection(): \Reflector
    {
        return new \ReflectionClass($this->name);
    }

    public function normalize(): array
    {
        return [
            'id' => $this->id->value,
            'name' => $this->name,
            'shortName' => $this->shortName,
            'namespace' => $this->namespace,
            'hash' => $this->hash,
            'fileName' => $this->fileName,
            'fileHash' => $this->fileHash,
            'attributes' => $this->normalizeAttributes(),
            'properties' => $this->normalizeProperties(),
            'methods' => $this->normalizeMethods(),
        ];
    }

    public static function denormalize(array $data): static
    {
        $instance = static::recreate(
            ReflectionId::recreate($data['id']),
            $data['name'],
            $data['shortName'],
            $data['namespace'],
            $data['hash'],
            $data['fileName'],
            $data['fileHash'],
            static::denormalizeAttributes($data['attributes']),
            static::denormalizeProperties($data['properties']),
            static::denormalizeMethods($data['methods']),
        );

        foreach ($instance->attributes() as $attrs) {
            foreach ($attrs as $attr) {
                $attr->parent($instance);
            }
        }

        foreach ($instance->properties() as $prop) {
            $prop->parent($instance);
        }

        foreach ($instance->methods() as $method) {
            $method->parent($instance);
        }

        return $instance;
    }
}
