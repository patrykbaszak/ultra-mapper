<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Domain;

use PBaszak\UltraMapper\Build\Domain\Events\BuildCreated;
use PBaszak\UltraMapper\Build\Domain\Identity\BuildId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\AggregateRoot;

final class Build extends AggregateRoot
{
    private function __construct(
        private BuildId $id,
    ) {
    }

    public static function create(BuildId $id): static
    {
        $build = new static($id);

        $build->raise(
            new BuildCreated($id)
        );

        return $build;
    }

    public static function recreate(BuildId $id): static
    {
        return new static($id);
    }

    public function id(): BuildId
    {
        return $this->id;
    }
}
