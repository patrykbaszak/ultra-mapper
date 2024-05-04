<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Application\Enum\ClassType;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\ClassNotFoundException;
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
    public false|string $filePath;
    public string $hash;
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
        try {
            $reflection = new \ReflectionClass($class);
            /* @phpstan-ignore-next-line */
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException(sprintf('Class %s not found. %s', $class, $e->getMessage()), 5921, $e);
        }
        $instance = new self();

        $instance->parent = $parent;
        $instance->name = $reflection->getName();
        $instance->shortName = $reflection->getShortName();
        $instance->namespace = $reflection->getNamespaceName();

        $instance->blueprintName = $reflection->isAnonymous() ? md5($instance->name) : strtolower(str_replace('\\', '_', $instance->name));

        $instance->filePath = $reflection->getFileName();
        $instance->hash = md5($reflection->__toString());
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
