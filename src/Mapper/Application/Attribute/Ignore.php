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

    public const DENORMALIZATION = 1; // 0001
    public const NORMALIZATION = 2; // 0010
    public const MAPPING = 4; // 0100
    public const TRANSFORMATION = 8; // 1000

    public const PROCESS_TYPE_MAP = [
        Process::DENORMALIZATION_PROCESS => self::DENORMALIZATION,
        Process::NORMALIZATION_PROCESS => self::NORMALIZATION,
        Process::MAPPING_PROCESS => self::MAPPING,
        Process::TRANSFORMATION_PROCESS => self::TRANSFORMATION,
    ];

    /**
     * @param array<string, mixed> $options Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public int $processType = self::DENORMALIZATION | self::NORMALIZATION | self::TRANSFORMATION | self::MAPPING,
        public readonly array $options = []
    ) {
    }

    public function validate(\ReflectionProperty|\ReflectionParameter|\ReflectionClass $reflection): void
    {
        // todo implement
    }
}
