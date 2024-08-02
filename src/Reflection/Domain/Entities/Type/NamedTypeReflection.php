<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities\Type;

final class NamedTypeReflection extends TypeReflection
{
    public const IS_BUILT_IN = 0;
    public const IS_CLASS = 1;
    public const IS_INTERFACE = 2;
    public const IS_ABSTRACT = 4;
    public const IS_ENUM = 8;

    private function __construct(
        /** @var string|class-string */
        private string $name,
        private int $flags,
    ) {
    }

    /**
     * @param string|class-string $name
     */
    public static function create(
        string $name,
        int $flags,
    ): static {
        if (($flags & self::IS_CLASS) == self::IS_CLASS && !class_exists($name, false)) {
            throw new \InvalidArgumentException("Class $name does not exist.");
        }

        if (($flags & self::IS_INTERFACE) == self::IS_INTERFACE && !interface_exists($name, false)) {
            throw new \InvalidArgumentException("Interface $name does not exist.");
        }

        if (($flags & self::IS_ABSTRACT) == self::IS_ABSTRACT && !class_exists($name, false)) {
            throw new \InvalidArgumentException("Abstract class $name does not exist.");
        }

        if (($flags & self::IS_ABSTRACT) == self::IS_ABSTRACT && !(new \ReflectionClass($name))->isAbstract()) {
            throw new \InvalidArgumentException("Class $name is not abstract.");
        }

        if (($flags & self::IS_ENUM) == self::IS_ENUM && !class_exists($name, false)) {
            throw new \InvalidArgumentException("Enum $name does not exist.");
        }

        return new static($name, $flags);
    }

    /**
     * @param string|class-string $name
     */
    public static function recreate(
        string $name,
        int $flags,
    ): static {
        return new static($name, $flags);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function flags(): int
    {
        return $this->flags;
    }

    public function isBuiltIn(): bool
    {
        return ($this->flags & self::IS_BUILT_IN) == self::IS_BUILT_IN;
    }

    public function isClass(): bool
    {
        return ($this->flags & self::IS_CLASS) == self::IS_CLASS;
    }

    public function isInterface(): bool
    {
        return ($this->flags & self::IS_INTERFACE) == self::IS_INTERFACE;
    }

    public function isAbstractClass(): bool
    {
        return ($this->flags & self::IS_ABSTRACT) == self::IS_ABSTRACT;
    }

    public function isEnum(): bool
    {
        return ($this->flags & self::IS_ENUM) == self::IS_ENUM;
    }

    public function normalize(): array
    {
        return [
            'type' => __CLASS__,
            'name' => $this->name,
            'flags' => [
                'value' => $this->flags,
                'is_built_in' => ($this->flags & self::IS_BUILT_IN) == self::IS_BUILT_IN,
                'is_class' => ($this->flags & self::IS_CLASS) == self::IS_CLASS,
                'is_interface' => ($this->flags & self::IS_INTERFACE) == self::IS_INTERFACE,
                'is_abstract' => ($this->flags & self::IS_ABSTRACT) == self::IS_ABSTRACT,
                'is_enum' => ($this->flags & self::IS_ENUM) == self::IS_ENUM,
            ],
        ];
    }

    public static function denormalize(array $data): static
    {
        return static::recreate(
            $data['name'],
            $data['flags']['value']
        );
    }
}
