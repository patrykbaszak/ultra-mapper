<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Events;

use PBaszak\UltraMapper\Shared\Domain\Identity\Identifier;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\Event;

final readonly class MapperCreated extends Event
{
    public const EVENT_NAME = 'mapper_created';
    public const EVENT_VERSION = 1;

    public function __construct(
        public readonly Identifier $id
    ) {
        parent::__construct($id, self::EVENT_NAME, self::EVENT_VERSION);
    }
}
