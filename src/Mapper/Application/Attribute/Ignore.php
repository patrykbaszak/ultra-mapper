<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Ignore implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    /**
     * @param array<string, mixed> $options Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public int $processType = self::DENORMALIZATION | self::NORMALIZATION | self::TRANSFORMATION | self::MAPPING,
        public readonly array $options = []
    ) {
    }

    public function validate(\Reflector $reflector): void
    {
        // todo implement
    }

    public function getProcessType(): int
    {
        return $this->processType;
    }
}
