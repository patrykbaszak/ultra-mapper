<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class TargetProperty implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    public const MAPPING = 0; // 00
    public const DENORMALIZATION = 1; // 01
    public const NORMALIZATION = 2; // 10

    /**
     * @param string               $name       the name of the target property
     * @param int                  $useNameFor the target property name will be used for mapping, denormalization, normalization or all of them
     * @param ?string              $path       The path to the target property. If null, the name of the target property is used.
     * @param array<string, mixed> $options    Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public readonly string $name,
        public readonly int $useNameFor = self::MAPPING | self::DENORMALIZATION | self::NORMALIZATION,
        public readonly ?string $path = null,
        public bool $useForDenormalization = true,
        public bool $useForMapping = true,
        public bool $useForNormalization = true,
        public readonly array $options = []
    ) {
    }

    public function validate(\ReflectionProperty|\ReflectionClass $reflection): void
    {
        // todo implement
    }
}
