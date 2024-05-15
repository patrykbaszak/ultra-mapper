<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class MaxDepth implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    /**
     * @param int                  $maxDepth the maximum depth of the object graph that should be mapped
     * @param mixed                $fillWith the value that should be used to fill the property if the depth is exceeded
     * @param array<string, mixed> $options  Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public readonly int $maxDepth,
        public mixed $fillWith = null,
        public readonly array $options = []
    ) {
    }

    public function validate(\ReflectionProperty|\ReflectionClass $reflection): void
    {
        // todo implement
    }
}
