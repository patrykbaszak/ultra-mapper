<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Shared\Infrastructure\Normalization;

interface Normalizable
{
    /**
     * Normalize the object to an array.
     *
     * @return array<string, mixed>
     */
    public function normalize(): array;

    /**
     * Denormalize the object from an array.
     *
     * @param array<string, mixed> $data
     */
    public static function denormalize(array $data): static;
}
