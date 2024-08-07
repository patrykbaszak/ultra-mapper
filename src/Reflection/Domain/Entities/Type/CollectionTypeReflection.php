<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities\Type;

final class CollectionTypeReflection extends TypeReflection
{
    private function __construct(
        private NamedTypeReflection $collectionType,
        private TypeReflection $keyType,
        private TypeReflection $valueType,
    ) {
    }

    public static function create(
        NamedTypeReflection $collectionType,
        TypeReflection $keyType,
        TypeReflection $valueType,
    ): static {
        return new static($collectionType, $keyType, $valueType);
    }

    public static function recreate(
        NamedTypeReflection $collectionType,
        TypeReflection $keyType,
        TypeReflection $valueType,
    ): static {
        return new static($collectionType, $keyType, $valueType);
    }

    public function allowsNull(): bool
    {
        return false;
    }

    public function normalize(): array
    {
        return [
            'type' => __CLASS__,
            'types' => [
                'collectionType' => $this->collectionType->normalize(),
                'keyType' => $this->keyType->normalize(),
                'valueType' => $this->valueType->normalize(),
            ],
        ];
    }

    public static function denormalize(array $data): static
    {
        return static::recreate(
            NamedTypeReflection::denormalize($data['collectionType']),
            $data['keyType']['type']::denormalize($data['keyType']),
            $data['valueType']['type']::denormalize($data['valueType']),
        );
    }
}
