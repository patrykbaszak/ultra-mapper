<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Application\Enum\ClassType;
use PBaszak\UltraMapper\Blueprint\Domain\Serializer\Normalizable;
use PBaszak\UltraMapper\Blueprint\Domain\Serializer\NormalizeTrait;

/**
 * The representation of the class.
 */
class Blueprint implements Normalizable
{
    use NormalizeTrait;

    public ?Property $parent;
    public string $blueprintName;

    /** @var class-string */
    public string $name;
    public string $shortName;
    public string $namespace;
    public string $filePath;
    public string $fileHash;
    public ClassType $type;
    public false|string $docBlock;

    /** @var array<class-string, Attribute> */
    public array $attributes;

    /** @var array<class-string, Property> */
    public array $properties;

    /** @var array<class-string, Method> */
    public array $methods;

    /**
     * @param class-string $class
     */
    public static function create(string $class, ?Property $parent): self
    {
        $reflection = new \ReflectionClass($class);
        $instance = new self();

        $instance->parent = $parent;
        $instance->name = $reflection->getName();
        $instance->shortName = $reflection->getShortName();
        $instance->namespace = $reflection->getNamespaceName();

        $instance->blueprintName = strtolower(str_replace('\\', '_', $instance->name));

        $instance->filePath = $reflection->getFileName() ?: throw new \RuntimeException('File path not found.');
        $instance->fileHash = md5_file($instance->filePath) ?: throw new \RuntimeException('File hash not found.');
        $instance->type = match (true) {
            $reflection->isAbstract() => ClassType::ABSTRACT,
            $reflection->isEnum() => ClassType::ENUM,
            $reflection->isInterface() => ClassType::INTERFACE,
            $reflection->isTrait() => ClassType::TRAIT,
            default => ClassType::STANDARD,
        };
        $instance->docBlock = $reflection->getDocComment();

        $instance->attributes = [];
        $instance->properties = [];
        $instance->methods = [];

        return $instance;
    }

    public function getReflection(): \ReflectionClass
    {
        return new \ReflectionClass($this->name);
    }
}
