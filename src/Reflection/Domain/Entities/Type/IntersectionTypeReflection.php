<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities\Type;

final class IntersectionTypeReflection extends TypeReflection
{
    private function __construct(
        /** @var array<TypeReflection> */
        private array $types,
    ) {
    }

    /**
     * @param array<TypeReflection> $types
     */
    public static function create(
        array $types,
    ): static {
        return new static($types);
    }

    /**
     * @param array<TypeReflection> $types
     */
    public static function recreate(
        array $types,
    ): static {
        return new static($types);
    }

    public function allowsNull(): bool
    {
        return false;
    }

    public function normalize(): array
    {
        return [
            'type' => __CLASS__,
            'types' => array_map(
                fn (TypeReflection $type) => $type->normalize(),
                $this->types
            ),
        ];
    }

    public static function denormalize(array $data): static
    {
        return static::recreate(
            array_map(
                fn (array $type) => $type['type']::denormalize($type),
                $data['types']
            )
        );
    }
}
