<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Matcher\Extender;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;
use PBaszak\UltraMapper\Mapper\Domain\Modules\Extender\Contract\ExtenderStrategyInterface;

class DiscriminatorExtender implements ExtenderStrategyInterface
{
    private Blueprint $blueprint;
    private bool $extended;

    public function extend(Blueprint $blueprint, Process $process, Context $context, string $processUse): bool
    {
        $this->extended = false;
        $this->blueprint = $blueprint;

        array_walk(
            $blueprint->blueprints,
            fn (ClassBlueprint $classBlueprint) => $this->handleClassBlueprint($classBlueprint, $process, $context)
        );

        return $this->extended;
    }

    private function handleClassBlueprint(ClassBlueprint $blueprint, Process $process, Context $context): void
    {
        /** @var PropertyBlueprint $property */
        foreach ($blueprint->properties as $property) {
        }
    }
}
