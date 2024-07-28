<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Events;

use PBaszak\UltraMapper\Reflection\Domain\Exception\ReflectionException;
use PBaszak\UltraMapper\Shared\Domain\Identity\Identifier;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\Event;

final readonly class ReflectionAdded extends Event
{
    public const EVENT_VERSION = 1;

    public const ATTRIBUTE_REFLECTION_ADDED = 'attribute_reflection_added';
    public const CLASS_REFLECTION_ADDED = 'class_reflection_added';
    public const METHOD_REFLECTION_ADDED = 'method_reflection_added';
    public const PARAMETER_REFLECTION_ADDED = 'parameter_reflection_added';
    public const PROPERTY_REFLECTION_ADDED = 'property_reflection_added';
    public const TYPE_REFLECTION_ADDED = 'type_reflection_added';

    private const REFLECTIONS = [
        self::ATTRIBUTE_REFLECTION_ADDED,
        self::CLASS_REFLECTION_ADDED,
        self::METHOD_REFLECTION_ADDED,
        self::PARAMETER_REFLECTION_ADDED,
        self::PROPERTY_REFLECTION_ADDED,
        self::TYPE_REFLECTION_ADDED,
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
