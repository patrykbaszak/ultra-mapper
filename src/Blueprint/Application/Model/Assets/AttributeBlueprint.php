<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Model\Assets;

use PBaszak\UltraMapper\Blueprint\Application\Exception\BlueprintException;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

class AttributeBlueprint implements Normalizable
{
    public ClassBlueprint|PropertyBlueprint $parent;

    /** @var class-string */
    public string $class;
    /** @var array<string|int, mixed> */
    public array $arguments;

    public false|string $filePath;
    public false|string $fileHash;

    public static function create(\ReflectionAttribute $attribute, ClassBlueprint|PropertyBlueprint $parent): self
    {
        $instance = new self();
        $instance->parent = $parent;
        $instance->class = $attribute->getName();
        $instance->arguments = $attribute->getArguments();

        $ref = (new \ReflectionClass($instance->class));
        $instance->filePath = $ref->getFileName();
        $instance->fileHash = $instance->filePath ? md5_file($instance->filePath) : false;

        if ($instance->hasDeclarationFile()) {
            /* @phpstan-ignore-next-line types of $instance->filePath and $instance->fileHash are for sure strings */
            Blueprint::getBlueprint($parent)?->addFileHash($instance->filePath, $instance->fileHash);
        }

        return $instance;
    }

    public static function createCollection(ClassBlueprint|ParameterBlueprint|PropertyBlueprint $parent): AssetsAggregate
    {
        $ref = $parent->getReflection();

        $attributes = [];
        foreach ($ref->getAttributes() as $attribute) {
            $attributes[$attribute->getName()][] = static::create($attribute, $parent);
        }

        return new AssetsAggregate($parent, $attributes);
    }

    public function isPropertyAttribute(): bool
    {
        return $this->parent instanceof PropertyBlueprint;
    }

    public function isBlueprintAttribute(): bool
    {
        return $this->parent instanceof ClassBlueprint;
    }

    public function newInstance(): object
    {
        return new $this->class(...$this->arguments);
    }

    public function getReflection(): \ReflectionAttribute
    {
        $attr = $this->parent->getReflection()->getAttributes($this->class);
        if (0 === count($attr)) {
            throw new BlueprintException(sprintf('Attribute %s not found on %s.', $this->class, $this->parent->getReflection()->getName()), 'This should not happen. Please report this issue.', 5921);
        }
        if (1 === count($attr)) {
            return $attr[0];
        }
        foreach ($attr as $a) {
            if ($this->arguments === $a->getArguments()) {
                return $a;
            }
        }

        throw new BlueprintException(sprintf('Attribute %s not found on %s.', $this->class, $this->parent->getReflection()->getName()), 'This should not happen. Please report this issue.', 5924);
    }

    public function hasDeclarationFile(): bool
    {
        return false !== $this->filePath;
    }

    public function normalize(): array
    {
        return [
            'class' => $this->class,
            'arguments' => $this->arguments,
        ];
    }
}
