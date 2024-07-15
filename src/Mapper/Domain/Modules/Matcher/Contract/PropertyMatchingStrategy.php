<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Modules\Matcher\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

interface PropertyMatchingStrategy
{
    /**
     * Method checks if the strategy conditions are met.
     *
     * @return bool success if the conditions are met
     */
    public function isStrategyConditionsMet(
        Context $context,
        Process $process,
        PropertyBlueprint $origin,
        PropertyBlueprint $source,
        PropertyBlueprint $target
    ): bool;

    /**
     * Method matches properties in the way defined by the strategy.
     */
    public function matchProperties(
        Context $context,
        Process $process,
        PropertyBlueprint $origin,
        PropertyBlueprint $source,
        PropertyBlueprint $target
    ): void;
}
