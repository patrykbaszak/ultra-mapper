<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entities;

use PBaszak\UltraMapper\Blueprint\Domain\Events\BlueprintCreated;
use PBaszak\UltraMapper\Blueprint\Domain\Identity\BlueprintId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\AggregateRoot;

final class ClassBlueprint extends AggregateRoot
{
    private function __construct(
        private BlueprintId $id,
        /** @var array<string, PropertyBlueprint> */
        private array $propertyBlueprints = [],
    ) {
    }

    /**
     * @param array<string, PropertyBlueprint> $propertyBlueprints
     */
    public static function create(BlueprintId $id, array $propertyBlueprints): static
    {
        $blueprint = new static($id, $propertyBlueprints);

        $blueprint->raise(
            new BlueprintCreated($id)
        );

        return $blueprint;
    }

    /**
     * @param array<string, PropertyBlueprint> $propertyBlueprints
     */
    public static function recreate(BlueprintId $id, array $propertyBlueprints): static
    {
        return new static($id, $propertyBlueprints);
    }

    public function id(): BlueprintId
    {
        return $this->id;
    }

    /**
     * @return array<string, PropertyBlueprint>
     */
    public function propertyBlueprints(): array
    {
        return $this->propertyBlueprints;
    }
}
