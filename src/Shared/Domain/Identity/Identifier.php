<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Shared\Domain\Identity;

use Symfony\Component\Uid\Uuid;

abstract class Identifier
{
    final protected function __construct(
        public readonly string $value
    ) {
    }

    public static function create(string $value): static
    {
        return new static($value);
    }

    public static function recreate(string $value): static
    {
        return new static($value);
    }

    public static function uuid(): static
    {
        return new static(Uuid::v7()->toRfc4122());
    }
}
