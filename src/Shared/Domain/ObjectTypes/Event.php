<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Shared\Domain\ObjectTypes;

use PBaszak\UltraMapper\Shared\Domain\Identity\Identifier;

abstract readonly class Event
{
    public function __construct(
        private Identifier $identifier,
        private string $eventName,
        private int $version
    ) {
    }

    public function aggregateId(): Identifier
    {
        return $this->identifier;
    }

    public function eventVersion(): int
    {
        return $this->version;
    }

    public function eventName(): string
    {
        return $this->eventName;
    }
}
