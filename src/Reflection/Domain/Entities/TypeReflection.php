<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces\ReflectionInterface;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionCreated;
use PBaszak\UltraMapper\Reflection\Domain\Identity\ReflectionId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\AggregateRoot;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

final class TypeReflection extends AggregateRoot implements Normalizable, ReflectionInterface
{
    private function __construct(
        private ReflectionId $id,
    ) {
    }

    public static function create(ReflectionId $id): static
    {
        $reflection = new static($id);

        $reflection->raise(
            new ReflectionCreated($id)
        );

        return $reflection;
    }

    public static function recreate(ReflectionId $id): static
    {
        return new static($id);
    }

    public function id(): ReflectionId
    {
        return $this->id;
    }
}
