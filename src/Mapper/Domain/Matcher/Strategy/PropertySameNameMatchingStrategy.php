<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Matcher\Strategy;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Matcher\Contract\PropertyMatchingStrategy;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

class PropertySameNameMatchingStrategy implements PropertyMatchingStrategy
{
    public function isStrategyConditionsMet(
        Context $context,
        Process $process,
        PropertyBlueprint $origin,
        PropertyBlueprint $source,
        PropertyBlueprint $target
    ): bool {
    }

    public function matchProperties(
        Context $context,
        Process $process,
        PropertyBlueprint $origin,
        PropertyBlueprint $source,
        PropertyBlueprint $target
    ): void {
    }
}
