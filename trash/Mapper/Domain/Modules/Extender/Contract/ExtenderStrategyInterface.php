<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Modules\Extender\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

interface ExtenderStrategyInterface
{
    /**
     * Extend the blueprint with additional classes and properties based
     * on the class and properties attributes.
     *
     * @return bool `true` if the blueprint was extended, `false` otherwise
     */
    public function extend(Blueprint $blueprint, Process $process, Context $context, string $processUse): bool;
}
