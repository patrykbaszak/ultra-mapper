<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Accessor implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    /**
     * @param string|null          $getter  the getter method name
     * @param string|null          $setter  the setter method name
     * @param array<string, mixed> $options Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public readonly ?string $getter = null,
        public readonly ?string $setter = null,
        public readonly int $processType = self::DENORMALIZATION | self::NORMALIZATION | self::TRANSFORMATION | self::MAPPING,
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
