<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Serializer;

interface Normalizable
{
    /**
     * Normalize the object.
     *
     * @return array<string, mixed>
     */
    public function normalize(): array;

    /**
     * Denormalize the object.
     *
     * @param array<string, mixed> $data
     */
    public function denormalize(array $data): self;
}
