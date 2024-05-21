<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Contract;

use PBaszak\UltraMapper\Build\Application\Model\Blueprints;

interface MatcherInterface
{
    public function matchBlueprints(Blueprints $blueprints): void;
}
