<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Ignore implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    /**
     * @param array<string, mixed> $options Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public readonly array $options = []
    ) {
    }

    public function validate(\ReflectionProperty|\ReflectionClass $reflection): void
    {
        // todo implement
    }
}
