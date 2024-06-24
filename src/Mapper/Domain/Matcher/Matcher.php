<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Matcher;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Attribute\Groups;
use PBaszak\UltraMapper\Mapper\Application\Attribute\Ignore;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Matcher\Contract\ClassMatchingStrategy;
use PBaszak\UltraMapper\Mapper\Domain\Matcher\Contract\MatcherInterface;
use PBaszak\UltraMapper\Mapper\Domain\Matcher\Contract\PropertyMatchingStrategy;
use PBaszak\UltraMapper\Mapper\Domain\Matcher\Service\LoopDetector;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;
use Symfony\Component\Uid\Uuid;

/**
 * Class Matcher contains methods required by interface for external access,
 * but also contains methods useful for matching strategies. 
 */
class Matcher implements MatcherInterface
{
    /**
     * @param PropertyMatchingStrategy[]|class-string<PropertyMatchingStrategy>[] $propertyMatchingStrategies
     * @param ClassMatchingStrategy[]|class-string<ClassMatchingStrategy>[] $classMatchingStrategies
     */
    public function __construct(
        /** @var PropertyMatchingStrategy[] */
        protected array $propertyMatchingStrategies,
        /** @var ClassMatchingStrategy[] */
        protected array $classMatchingStrategies
    ) {
        array_walk(
            $this->propertyMatchingStrategies,
            fn (string|PropertyMatchingStrategy $strategy): PropertyMatchingStrategy => is_string($strategy) ? new $strategy() : $strategy
        );

        array_walk(
            $this->classMatchingStrategies,
            fn (string|ClassMatchingStrategy $strategy): ClassMatchingStrategy => is_string($strategy) ? new $strategy() : $strategy
        );
    }

    public function matchBlueprints(
        Context $context,
        Process $process,
        Blueprint $origin,
        Blueprint $source,
        Blueprint $target
    ): void {
        $blueprints = [$origin, $source, $target];
        $this->checkForLoop($context, $process, $blueprints);
        $this->addLinks(...$blueprints);
        array_walk($blueprints, fn (Blueprint $blueprint): ClassBlueprint => $blueprint->blueprints[$blueprint->root]);
        $this->matchClassBlueprints($context, $process, ...$blueprints);
    }

    /**
     * Method matches properties of the class blueprints and returns success of the operation.
     */
    public function matchProperties(
        Context $context,
        Process $process,
        PropertyBlueprint $origin,
        ClassBlueprint $source,
        ClassBlueprint $target
    ): bool {
        foreach ($source->properties as $sourceProperty) {
            foreach ($target->properties as $targetProperty) {
                $requiredMatches = $process->count();
                foreach ($this->propertyMatchingStrategies as $strategy) {
                    if ($strategy->isStrategyConditionsMet($context, $process, $origin, $sourceProperty, $targetProperty)) {
                        $strategy->matchProperties($context, $process, $origin, $sourceProperty, $targetProperty);
                        $requiredMatches--;
                    }

                    if ($requiredMatches === 0) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Method matches class blueprints based on the property types.
     */
    public function matchClassBlueprintsBasedOnProperty(
        Context $context,
        Process $process,
        PropertyBlueprint $origin,
        PropertyBlueprint $source,
        PropertyBlueprint $target
    ): void {
        $originClasses = $origin->type->getAllClassTypes();
        /** @var ClassBlueprint[] $originClasses */
        array_walk($originClasses, fn (string $originClass): ClassBlueprint => Blueprint::getBlueprint($origin)->blueprints[$originClass]);
        $sourceClasses = $source->type->getAllClassTypes();
        /** @var ClassBlueprint[] $sourceClasses */
        array_walk($sourceClasses, fn (string $sourceClass): ClassBlueprint => Blueprint::getBlueprint($origin)->blueprints[$sourceClass]);
        $targetClasses = $target->type->getAllClassTypes();
        /** @var ClassBlueprint[] $targetClasses */
        array_walk($targetClasses, fn (string $targetClass): ClassBlueprint => Blueprint::getBlueprint($origin)->blueprints[$targetClass]);

        foreach ($originClasses as $originClass) {
            foreach ($sourceClasses as $sourceClass) {
                foreach ($targetClasses as $targetClass) {
                    foreach ($this->classMatchingStrategies as $strategy) {
                        if ($strategy->isStrategyConditionsMet($context, $process, $originClass, $sourceClass, $targetClass)) {
                            $strategy->matchClasses($context, $process, $originClass, $sourceClass, $targetClass);
                        }
                    }
                }
            }
        }
    }

    /**
     * Method matches class blueprints and returns percentage of matched properties.
     */
    public function matchClassBlueprints(
        Context $context,
        Process $process,
        ClassBlueprint $origin,
        ClassBlueprint $source,
        ClassBlueprint $target
    ): float {
        $totalProperties = count($origin->properties->assets);
        $matchedProperties = 0;
        foreach ($origin->properties as $index => $property) {
            if ($this->isPropertyIgnored($context, $process, $property)) {
                $totalProperties--;
                unset($origin->properties[$index]);
                continue;
            }

            if ($this->matchProperties($context, $process, $property, $source, $target)) {
                $matchedProperties++;
            }
        }

        return $matchedProperties / $totalProperties;
    }

    /**
     * Method add links between blueprints.
     */
    public function addLinks(
        Blueprint|PropertyBlueprint|ClassBlueprint $origin,
        Blueprint|PropertyBlueprint|ClassBlueprint $source,
        Blueprint|PropertyBlueprint|ClassBlueprint $target
    ): void {
        $blueprints = [$origin, $source, $target];
        array_walk(
            $blueprints,
            function (object $object) {
                $object->options[self::OPTION_ID] = Uuid::v4()->toRfc4122();
            }
        );

        array_walk(
            $blueprints,
            function (object $object) use ($origin, $source, $target) {
                $object->options = array_replace_recursive(
                    $object->options,
                    [
                        self::OPTION_ORIGIN => $origin,
                        self::OPTION_SOURCE => $source,
                        self::OPTION_TARGET => $target,
                    ]
                );
            }
        );
    }

    /**
     * Method checks if the property should be ignored.
     * Only origin property should be checked.
     * 
     * @return bool Success if the property should be ignored.
     */
    protected function isPropertyIgnored(Context $context, Process $process, PropertyBlueprint $origin): bool
    {
        /** @var Groups[] $groups */
        $groups = $origin->getAttributes(Groups::class);
        $groups = array_filter(
            $groups,
            fn (Groups $groupsAttr): bool => $process->isAttributeMatchWithProcess($groupsAttr)
        );

        /** @var Ignore[] $ignored */
        $ignored = $origin->getAttributes(Ignore::class);
        $ignored = array_filter(
            $ignored,
            fn (Ignore $ignoreAttr): bool => $process->isAttributeMatchWithProcess($ignoreAttr)
        );

        if (!empty($groups) && !empty($ignored)) {
            throw new \LogicException('Property cannot be in group and ignored at the same time in the same process.');
        }

        if (!empty($ignored)) {
            return true;
        }

        if (!empty($groups)) {
            $actualGroups = [];
            foreach ($groups as $group) {
                $actualGroups = array_merge($actualGroups, $group->groups);
            }
            $actualGroups = array_unique($actualGroups);

            return !$context->isGroupMatching($actualGroups);
        }

        return false;
    }

    /**
     * @param Blueprint[] $blueprints
     */
    protected function checkForLoop(Context $context, Process $process, array $blueprints): void
    {
        array_walk($blueprints, fn (Blueprint $blueprint) => (new LoopDetector())->checkBlueprint($blueprint, $process));
    }
}
