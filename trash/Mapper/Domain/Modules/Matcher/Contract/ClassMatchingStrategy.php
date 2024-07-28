<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Modules\Matcher\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

interface ClassMatchingStrategy
{
    /**
     * Method checks if the strategy conditions are met.
     *
     * @return bool success if the conditions are met
     */
    public function isStrategyConditionsMet(
        Context $context,
        Process $process,
        ClassBlueprint $origin,
        ClassBlueprint $source,
        ClassBlueprint $target
    ): bool;

    /**
     * Method matches classes in the way defined by the strategy.
     */
    public function matchClasses(
        Context $context,
        Process $process,
        ClassBlueprint $origin,
        ClassBlueprint $source,
        ClassBlueprint $target
    ): void;
}
