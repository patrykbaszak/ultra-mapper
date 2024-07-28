<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain;

use PBaszak\UltraMapper\Mapper\Domain\Events\MapperCreated;
use PBaszak\UltraMapper\Mapper\Domain\Identity\MapperId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\AggregateRoot;

final class Mapper extends AggregateRoot
{
    private function __construct(
        private MapperId $id,
    ) {
    }

    public static function create(MapperId $id): static
    {
        $mapper = new static($id);

        $mapper->raise(
            new MapperCreated($id)
        );

        return $mapper;
    }

    public static function recreate(MapperId $id): static
    {
        return new static($id);
    }

    public function id(): MapperId
    {
        return $this->id;
    }
}
