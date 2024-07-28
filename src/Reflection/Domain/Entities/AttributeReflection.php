<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\AttributesSupport;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\ReflectionInterface;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionCreated;
use PBaszak\UltraMapper\Reflection\Domain\Exception\ReflectionException;
use PBaszak\UltraMapper\Reflection\Domain\Identity\ReflectionId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\AggregateRoot;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

final class AttributeReflection extends AggregateRoot implements Normalizable, ReflectionInterface
{
    private ReflectionInterface&AttributesSupport $parent;

    private function __construct(
        private ReflectionId $id,
        /** @var class-string */
        private string $name,
        private string $shortName,
        private string $namespace,
        /** @var array<string|int, mixed> */
        private array $arguments,
        private false|string $fileName,
        private false|string $fileHash,
        private false|string $docBlock,
    ) {
    }

    public static function create(
        \ReflectionAttribute $reflectionAttribute,
        ReflectionInterface&AttributesSupport $parent,
    ): static {
        $reflectionClass = new \ReflectionClass($reflectionAttribute->getName());

        $instance = new static(
            id: ReflectionId::uuid(),
            name: $reflectionClass->getName(),
            shortName: $reflectionClass->getShortName(),
            namespace: $reflectionClass->getNamespaceName(),
            arguments: $reflectionAttribute->getArguments(),
            fileName: $reflectionClass->getFileName(),
            fileHash: $reflectionClass->getFileName() ? md5_file($reflectionClass->getFileName()) : false,
            docBlock: $reflectionClass->getDocComment(),
        );

        $instance->parent = $parent;
        $instance->raise(
            new ReflectionCreated($instance->id())
        );

        $parent->addAttribute($instance);

        return $instance;
    }

    /**
     * @param ReflectionId $id
     * @param string $name
     * @param string $shortName
     * @param string $namespace
     * @param array<string|int, mixed> $arguments
     * @param false|string $fileName
     * @param false|string $fileHash
     * @param false|string $docBlock
     */
    public static function recreate(
        ReflectionId $id,
        string $name,
        string $shortName,
        string $namespace,
        array $arguments,
        false|string $fileName,
        false|string $fileHash,
        false|string $docBlock,
    ): static {
        return new static(
            id: $id,
            name: $name,
            shortName: $shortName,
            namespace: $namespace,
            arguments: $arguments,
            fileName: $fileName,
            fileHash: $fileHash,
            docBlock: $docBlock,
        );
    }

    public function parent(null|(ReflectionInterface&AttributesSupport) $parent = null): ReflectionInterface&AttributesSupport
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

    public function shortName(): string
    {
        return $this->shortName;
    }

    public function namespace(): string
    {
        return $this->namespace;
    }

    /** @return array<string|int, mixed> */
    public function arguments(): array
    {
        return $this->arguments;
    }

    public function fileName(): false|string
    {
        return $this->fileName;
    }

    public function fileHash(): false|string
    {
        return $this->fileHash;
    }

    public function docBlock(): false|string
    {
        return $this->docBlock;
    }

    public function instance(): object
    {
        return new $this->name(...$this->arguments);
    }

    public function reflection(): \Reflector
    {
        if (method_exists($parentReflection = $this->parent->reflection(), 'getAttributes')) {
            /**
             * @var \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter $parentReflection 
             * @var \ReflectionAttribute[] $attrs 
             */
            $attrs = $parentReflection->getAttributes($this->name);
            foreach ($attrs as $attr) {
                if ($attr->getArguments() === $this->arguments()) {
                    return $attr;
                }
            }
        }

        throw new ReflectionException(
            "ReflectionAttribute for `{$this->name}` in `{$parentReflection->__toString()}` class not found.",
            "Check if the attribute is still present in the class and if the arguments match.",
            5
        );
    }

    public function normalize(): array
    {
        return [
            'id' => $this->id->value,
            'name' => $this->name,
            'shortName' => $this->shortName,
            'namespace' => $this->namespace,
            'arguments' => $this->arguments,
            'fileName' => $this->fileName,
            'fileHash' => $this->fileHash,
            'docBlock' => $this->docBlock,
        ];
    }

    public static function denormalize(array $data): static
    {
        return static::recreate(
            ReflectionId::recreate($data['id']),
            $data['name'],
            $data['shortName'],
            $data['namespace'],
            $data['arguments'],
            $data['fileName'],
            $data['fileHash'],
            $data['docBlock'],
        );
    }
}
