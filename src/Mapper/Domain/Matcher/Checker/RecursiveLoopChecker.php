<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Matcher\Checker;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Attribute\Groups;
use PBaszak\UltraMapper\Mapper\Application\Attribute\Ignore;
use PBaszak\UltraMapper\Mapper\Application\Attribute\MaxDepth;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Matcher\Contract\BlueprintCheckerInterface;
use PBaszak\UltraMapper\Mapper\Domain\Matcher\Exception\BlueprintCheckerException;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

class RecursiveLoopChecker implements BlueprintCheckerInterface
{
    private Blueprint $blueprint;

    public function check(Blueprint $blueprint, Process $process, Context $context): void
    {
        $this->blueprint = $blueprint;
        foreach ($blueprint->blueprints as $index => $classBlueprint) {
            foreach ($process->processes as $processType) {
                $this->checkClassBlueprint($classBlueprint, $classBlueprint->name, $processType, $context);
            }
        }
    }

    private function checkClassBlueprint(ClassBlueprint $blueprint, string $actualCheckedClass, string $process, Context $context): void
    {
        /** @var PropertyBlueprint $property */
        foreach ($blueprint->properties as $property) {
            $types = $property->type->getAllClassTypes();
            if (empty($types)) {
                continue;
            }

            foreach ($types as $type) {
                if ($actualCheckedClass == $type) {
                    // so, type is repeated in one of the children properties
                    if (
                        // if there is no Ignore attribute
                        empty($property->getAttributes(Ignore::class, $process))
                        // and no MaxDepth attribute
                        && empty($property->getAttributes(MaxDepth::class, $process))
                        // and no Groups attribute
                        && (
                            empty($groups = $property->getAttributes(Groups::class, $process))
                            // or there is a Groups attribute and it's matching with the current context
                            || [false] !== array_values(array_unique(
                                array_map(fn (Groups $groupsAttr): bool => $context->isGroupMatching($groupsAttr->groups), $groups)
                            ))
                        )
                    ) {
                        // then we are forced to throw exception about recursive loop
                        throw new BlueprintCheckerException(sprintf('The %s::%s property is the recursive loop source.', $property->class->name, $property->originName), sprintf('There is a few ideas You can did right now. Use one of the following attributes for this property: Ignore, Groups with groups out of the current context (%s), MaxDepth.', implode(', ', $context->groups)));
                    }
                }

                foreach ($this->blueprint->blueprints as $classBlueprint) {
                    if ($type == $classBlueprint->name && $actualCheckedClass != $classBlueprint->name) {
                        $this->checkClassBlueprint($classBlueprint, $actualCheckedClass, $process, $context);
                    }
                }
            }
        }
    }
}
