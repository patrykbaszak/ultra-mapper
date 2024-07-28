<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entities;

use PBaszak\UltraMapper\Blueprint\Domain\Events\BlueprintCreated;
use PBaszak\UltraMapper\Blueprint\Domain\Identity\BlueprintId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\AggregateRoot;

final class PropertyBlueprint extends AggregateRoot
{
    private function __construct(
        private BlueprintId $id,
    ) {
    }

    public static function create(BlueprintId $id): static
    {
        $blueprint = new static($id);

        $blueprint->raise(
            new BlueprintCreated($id)
        );

        return $blueprint;
    }

    public static function recreate(BlueprintId $id): static
    {
        return new static($id);
    }

    public function id(): BlueprintId
    {
        return $this->id;
    }
}
