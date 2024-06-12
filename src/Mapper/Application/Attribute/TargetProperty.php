<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class TargetProperty implements AttributeInterface
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
     * @param string               $name       the name of the target property
     * @param int                  $useNameFor the target property name will be used for mapping, denormalization, normalization or all of them
     * @param ?string              $path       The path to the target property. If null, the name of the target property is used.
     * @param array<string, mixed> $options    Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public readonly string $name,
        public readonly int $useNameFor = self::DENORMALIZATION | self::NORMALIZATION | self::TRANSFORMATION | self::MAPPING,
        public readonly ?string $path = null,
        public readonly int $usePathFor = self::DENORMALIZATION | self::NORMALIZATION | self::TRANSFORMATION | self::MAPPING,
        public readonly array $options = []
    ) {
    }

    public function validate(\ReflectionProperty|\ReflectionParameter|\ReflectionClass $reflection): void
    {
        // there cannot be two target properties with the same processType
        // todo implement
    }
}
