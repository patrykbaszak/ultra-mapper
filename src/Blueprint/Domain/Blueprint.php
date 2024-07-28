<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain;

use PBaszak\UltraMapper\Blueprint\Domain\Events\BlueprintCreated;
use PBaszak\UltraMapper\Blueprint\Domain\Identity\BlueprintId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\AggregateRoot;

final class Blueprint extends AggregateRoot
{
    private function __construct(
        private BlueprintId $id,
        /** @var array<class-string, ClassBlueprint> */
        private array $classBlueprints = [],
    ) {
    }

    /**
     * @param BlueprintId $id
     * @param array<class-string, ClassBlueprint> $classBlueprints
     */
    public static function create(BlueprintId $id, array $classBlueprints): static
    {
        $blueprint = new static($id, $classBlueprints);

        $blueprint->raise(
            new BlueprintCreated($id)
        );

        return $blueprint;
    }

    /**
     * @param BlueprintId $id
     * @param array<class-string, ClassBlueprint> $classBlueprints
     */
    public static function recreate(BlueprintId $id, array $classBlueprints): static
    {
        return new static($id, $classBlueprints);
    }

    public function id(): BlueprintId
    {
        return $this->id;
    }

    /**
     * @return array<class-string, ClassBlueprint>
     */
    public function classBlueprints(): array
    {
        return $this->classBlueprints;
    }
}
