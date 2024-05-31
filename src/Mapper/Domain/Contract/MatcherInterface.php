<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Contract\MapperInterface;

interface MatcherInterface
{
    public const OPTION_ID = 'id';
    public const OPTION_PATH = 'path';
    public const OPTION_MIRROR = 'mirror';
    public const OPTION_ORIGIN = MapperInterface::BLUEPRINT_PROCESS_USE;
    public const OPTION_SOURCE = MapperInterface::FROM_PROCESS_USE;
    public const OPTION_TARGET = MapperInterface::TO_PROCESS_USE;

    /**
     * @param string<"normalization"|"denormalization"|"transformation"|"mapping"> $processType
     */
    public function matchBlueprints(string $processType, Blueprint $origin, Blueprint $source, Blueprint $target): void;
}
