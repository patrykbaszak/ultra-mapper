<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Contract;

use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

interface AttributeInterface
{
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

    public function validate(\ReflectionProperty|\ReflectionParameter|\ReflectionClass $reflection): void;

    public function getProcessType(): int;
}
