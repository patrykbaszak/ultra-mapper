<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Matcher\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Matcher\Exception\BlueprintExtenderException;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

interface BlueprintExtenderInterface
{
    /**
     * @throws BlueprintExtenderException
     */
    public function extend(Blueprint $blueprint, Process $process, Context $context): void;
}
