<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Matcher\Extender;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Attribute\Discriminator;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;
use PBaszak\UltraMapper\Mapper\Domain\Modules\Extender\Contract\ExtenderStrategyInterface;

class DiscriminatorExtender implements ExtenderStrategyInterface
{
    private Blueprint $blueprint;
    private bool $extended;

    public function extend(Blueprint $blueprint, Process $process, Context $context): bool
    {
        $this->blueprint = $blueprint;
        $this->extended = false;

        foreach ($process->processes as $processType) {
            array_walk(
                $blueprint->blueprints,
                fn (ClassBlueprint $classBlueprint) => $this->handleClassBlueprint($classBlueprint, $processType, $context)
            );
        }

        return $this->extended;
    }

    private function handleClassBlueprint(ClassBlueprint $blueprint, string $process, Context $context): void
    {
        /** @var PropertyBlueprint $property */
        foreach ($blueprint->properties as $property) {
            if (empty($discriminatorAttr = $property->getAttributes(Discriminator::class, $process))) {
                continue;
            }
        }
    }
}
