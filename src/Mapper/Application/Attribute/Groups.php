<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Groups implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    /**
     * @param string|array         $groups  the groups that the property should be included in
     * @param array<string, mixed> $options Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public readonly string|array $groups,
        public int $processType = self::DENORMALIZATION | self::NORMALIZATION | self::TRANSFORMATION | self::MAPPING,
        public readonly array $options = []
    ) {
    }

    public function validate(\ReflectionProperty|\ReflectionParameter|\ReflectionClass $reflection): void
    {
        // todo implement
    }
    
    public function getProcessType(): int
    {
        return $this->processType;
    }
}
