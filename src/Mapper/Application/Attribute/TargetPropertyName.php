<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class TargetPropertyName implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    /**
     * @param string               $name    the name of the target property
     * @param array<string, mixed> $options Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public readonly string $name,
        public readonly int $processType = self::DENORMALIZATION | self::NORMALIZATION | self::TRANSFORMATION | self::MAPPING,
        public readonly array $options = []
    ) {
    }

    public function validate(\Reflector $reflector): void
    {
        // there cannot be two target properties with the same processType
        // todo implement
    }

    public function getProcessType(): int
    {
        return $this->processType;
    }
}
