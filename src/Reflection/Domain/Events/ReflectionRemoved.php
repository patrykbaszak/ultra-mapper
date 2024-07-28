<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Events;

use PBaszak\UltraMapper\Reflection\Domain\Exception\ReflectionException;
use PBaszak\UltraMapper\Shared\Domain\Identity\Identifier;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\Event;

final readonly class ReflectionRemoved extends Event
{
    public const EVENT_VERSION = 1;

    public const ATTRIBUTE_REFLECTION_REMOVED = 'attribute_reflection_removed';
    public const CLASS_REFLECTION_REMOVED = 'class_reflection_removed';
    public const METHOD_REFLECTION_REMOVED = 'method_reflection_removed';
    public const PARAMETER_REFLECTION_REMOVED = 'parameter_reflection_removed';
    public const PROPERTY_REFLECTION_REMOVED = 'property_reflection_removed';
    public const TYPE_REFLECTION_REMOVED = 'type_reflection_removed';

    private const REFLECTIONS = [
        self::ATTRIBUTE_REFLECTION_REMOVED,
        self::CLASS_REFLECTION_REMOVED,
        self::METHOD_REFLECTION_REMOVED,
        self::PARAMETER_REFLECTION_REMOVED,
        self::PROPERTY_REFLECTION_REMOVED,
        self::TYPE_REFLECTION_REMOVED,
    ];

    public function __construct(
        public readonly Identifier $id,
        public readonly string $eventName,
    ) {
        if (!in_array($eventName, self::REFLECTIONS)) {
            throw new ReflectionException(
                'Invalid event name.',
                'Event name must be one of the following: ' . implode(', ', self::REFLECTIONS) . '.',
                3
            );
        }

        parent::__construct($id, $eventName, self::EVENT_VERSION);
    }
}
