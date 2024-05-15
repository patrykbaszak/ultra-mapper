<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

/**
 * Discriminator on the Interface or Abstract class level will
 * be used to determine the concrete class to instantiate.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS)]
class Discriminator implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    public const PROPERTY_FROM_DISCRIMINATED_CLASS = 1;
    public const PROPERTY_FROM_PARENT_CLASS = 2;

    /**
     * @param array<string, class-string> $map            [discriminatorValue => class]
     * @param string                      $discriminator  Property name that holds the discriminator value
     * @param int                         $propertySource Where to look for the discriminator property
     * @param array<string, mixed>        $options        Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public readonly array $map,
        public readonly string $discriminator,
        public readonly int $propertySource = self::PROPERTY_FROM_DISCRIMINATED_CLASS,
        public readonly array $options = []
    ) {
    }

    public function validate(\ReflectionProperty|\ReflectionClass $reflection): void
    {
        // todo implement
    }
}
