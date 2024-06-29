<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Matcher\Checker;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Matcher\Contract\BlueprintCheckerInterface;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

class RecursiveLoopChecker implements BlueprintCheckerInterface
{
    private Blueprint $blueprint;

    public function check(Blueprint $blueprint, Process $process, Context $context): void
    {
        $this->blueprint = $blueprint;
        
    }

    private function checkClassBlueprint(ClassBlueprint $blueprint, Process $process, Context $context): void
    {
        /** @var PropertyBlueprint $property */
        foreach ($blueprint->properties as $property) {
            
        }
    }
}