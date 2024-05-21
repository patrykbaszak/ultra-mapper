<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Service;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Build\Application\Model\Blueprints;
use PBaszak\UltraMapper\Mapper\Domain\Contract\MatcherInterface;

class Matcher implements MatcherInterface
{
    public function matchBlueprints(Blueprints $blueprints): void
    {
        // TODO: Implement matchBlueprints() method.
    }

    protected function doMatchBlueprints(Blueprint $origin, Blueprint $source, Blueprint $target): void
    {

    }

    protected function matchProperties(PropertyBlueprint $originProperty, Blueprint $source, Blueprint $target): void
    {

    }
}
