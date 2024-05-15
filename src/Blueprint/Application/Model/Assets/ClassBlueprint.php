<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Model\Assets;

use PBaszak\UltraMapper\Blueprint\Application\Enum\ClassType;
use PBaszak\UltraMapper\Blueprint\Application\Exception\BlueprintException;
use PBaszak\UltraMapper\Blueprint\Application\Exception\ClassNotFoundException;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

class ClassBlueprint implements Normalizable
{
    public ?Blueprint $blueprint;
    public ?PropertyBlueprint $parent;
    public string $blueprintName;

    /** @var class-string */
    public string $name;
    public string $shortName;
    public string $namespace;
    public false|string $filePath;
    public false|string $fileHash;
    public string $hash;
    public ClassType $type;
    public false|string $docBlock;

    public AssetsAggregate $attributes;
    public AssetsAggregate $properties;
    public AssetsAggregate $methods;

    /**
     * @param class-string $class
     */
    public static function create(string $class, ?PropertyBlueprint $parent, ?Blueprint $blueprint = null): self
    {
        if (__CLASS__ === $class) {
            throw new BlueprintException('Unable to create a Blueprint for the ClassBlueprint class. This would create an infinite loop.', 5922);
        }

        try {
            $reflection = new \ReflectionClass($class);
            /* @phpstan-ignore-next-line */
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException(sprintf('Class %s not found. %s', $class, $e->getMessage()), 5931, $e);
        }
        $instance = new self();
        $instance->blueprintName = $reflection->isAnonymous() ? md5($reflection->getName()) : strtolower(str_replace('\\', '_', $reflection->getName()));

        if ($blueprint && array_key_exists($instance->blueprintName, $blueprint->blueprints)) {
            return $blueprint->blueprints[$instance->blueprintName];
        }

        $instance->blueprint = $blueprint;
        $instance->parent = $parent;
        $instance->name = $reflection->getName();
        $instance->shortName = $reflection->getShortName();
        $instance->namespace = $reflection->getNamespaceName();

        $instance->filePath = $reflection->getFileName();
        $instance->fileHash = $instance->filePath ? md5_file($instance->filePath) : false;
        $instance->hash = md5($reflection->__toString());
        $instance->type = match (true) {
            $reflection->isAbstract() => ClassType::ABSTRACT,
            $reflection->isEnum() => ClassType::ENUM,
            $reflection->isInterface() => ClassType::INTERFACE,
            $reflection->isTrait() => ClassType::TRAIT,
            default => ClassType::STANDARD,
        };
        $instance->docBlock = $reflection->getDocComment();

        if ($blueprint) {
            $blueprint->addBlueprint($instance);
        } else {
            $blueprint = Blueprint::create($instance);
        }

        $instance->attributes = AttributeBlueprint::createCollection($instance);
        $instance->properties = PropertyBlueprint::createCollection($instance);
        $instance->methods = MethodBlueprint::createCollection($instance);

        return $instance;
    }

    public function getReflection(): \ReflectionClass
    {
        return new \ReflectionClass($this->name);
    }

    public function hasDeclarationFile(): bool
    {
        return false !== $this->filePath;
    }

    public function normalize(): array
    {
        return [
            'name' => $this->name,
            'shortName' => $this->shortName,
            'namespace' => $this->namespace,
            'filePath' => $this->filePath,
            'fileHash' => $this->fileHash,
            'hash' => $this->hash,
            'type' => $this->type->value,
            'docBlock' => $this->docBlock,
            'attributes' => $this->attributes->normalize(),
            'properties' => $this->properties->normalize(),
            'methods' => $this->methods->normalize(),
        ];
    }

    public function __clone(): void
    {
        $this->attributes = clone $this->attributes;
        $this->attributes->root = $this;
        foreach ($this->attributes as $attribute) {
            $attribute->parent = $this;
        }

        $this->properties = clone $this->properties;
        $this->properties->root = $this;
        foreach ($this->properties as $property) {
            $property->parent = $this;
        }

        $this->methods = clone $this->methods;
        $this->methods->root = $this;
        foreach ($this->methods as $method) {
            $method->parent = $this;
        }
    }
}
