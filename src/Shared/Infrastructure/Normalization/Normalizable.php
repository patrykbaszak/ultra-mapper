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
}
