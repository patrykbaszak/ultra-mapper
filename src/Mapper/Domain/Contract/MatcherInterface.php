<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Contract\MapperInterface;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

interface MatcherInterface
{
    public const OPTION_ID = 'id';
    public const OPTION_ORIGIN = MapperInterface::BLUEPRINT_PROCESS_USE;
    public const OPTION_SOURCE = MapperInterface::FROM_PROCESS_USE;
    public const OPTION_TARGET = MapperInterface::TO_PROCESS_USE;

    public function matchBlueprints(
        Context $context,
        Process $processType,
        Blueprint $origin,
        Blueprint $source,
        Blueprint $target
    ): void;
}
