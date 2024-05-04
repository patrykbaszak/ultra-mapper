<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Application\Enum\ClassType;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\AttributeAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\BlueprintAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\MethodAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\PropertyAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\ClassNotFoundException;

/**
 * The representation of the class.
 */
class Blueprint
{
    public ?BlueprintAggregate $aggregate;
    public ?Property $parent;
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

    public AttributeAggregate $attributes;
    public PropertyAggregate $properties;
    public MethodAggregate $methods;

    /**
     * @param class-string $class
     */
    public static function create(string $class, ?Property $parent, ?BlueprintAggregate $aggregate = null): self
    {
        try {
            $reflection = new \ReflectionClass($class);
            /* @phpstan-ignore-next-line */
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException(sprintf('Class %s not found. %s', $class, $e->getMessage()), 5931, $e);
        }
        $instance = new self();
        $instance->aggregate = $aggregate;
        $instance->parent = $parent;
        $instance->name = $reflection->getName();
        $instance->shortName = $reflection->getShortName();
        $instance->namespace = $reflection->getNamespaceName();

        $instance->blueprintName = $reflection->isAnonymous() ? md5($instance->name) : strtolower(str_replace('\\', '_', $instance->name));

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

        $instance->attributes = AttributeAggregate::create($instance);
        $instance->properties = PropertyAggregate::create($instance);
        $instance->methods = MethodAggregate::create($instance);

        if ($aggregate) {
            $aggregate->addBlueprint($instance);
        }

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
}
