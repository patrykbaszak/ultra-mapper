<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Shared\Domain\ObjectTypes;

use PBaszak\UltraMapper\Shared\Domain\Identity\Identifier;

abstract class Entity
{
    /**
     * @var Event[]
     */
    protected array $events = [];

    abstract public function id(): Identifier;

    /**
     * @return Event[]
     */
    final public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    final protected function raise(Event $event): void
    {
        $this->events[] = $event;
    }
}
