<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Service;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Contract\MatcherInterface;
use PBaszak\UltraMapper\Mapper\Domain\Exception\PropertyNotMatchedException;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;
use PBaszak\UltraMapper\Mapper\Domain\Service\Matcher\SameNameStrategy;
use PBaszak\UltraMapper\Mapper\Domain\Service\Matcher\TargetPropertyAttributeStrategy;
use Symfony\Component\Uid\Uuid;

class Matcher implements MatcherInterface
{
    /** @var class-string<Matcher\MatchingStrategyInterface>[] */
    protected const MATCHING_STRATEGIES = [
        TargetPropertyAttributeStrategy::class,
        SameNameStrategy::class,
    ];

    public function matchBlueprints(Context $context, Process $processType, Blueprint $origin, Blueprint $source, Blueprint $target): void
    {
        $this->addLinks($origin, $source, $target);
        $rootBlueprints = array_map(
            fn (Blueprint $blueprint) => $blueprint->blueprints[$blueprint->root],
            [$origin, $source, $target]
        );
        $this->matchClassBlueprints($processType, ...$rootBlueprints);
    }

    protected function matchClassBlueprints(Process $processType, ClassBlueprint $originClass, ClassBlueprint $source, ClassBlueprint $target): void
    {
        $this->addLinks($originClass, $source, $target);

        /** @var PropertyBlueprint $property */
        foreach ($originClass->properties->assets as $property) {
            $this->matchProperties($processType, $property, $source, $target);
        }
    }

    protected function matchProperties(Process $processType, PropertyBlueprint $originProperty, ClassBlueprint $source, ClassBlueprint $target): void
    {
        foreach ($source->properties as $sourceProperty) {
            foreach ($target->properties as $targetProperty) {
                foreach ($this::MATCHING_STRATEGIES as $strategy) {
                    $strategyInstance = new $strategy();
                    if (1 === $processType->count() && $strategyInstance->confirmPropertiesMatching($processType->processes[0], $originProperty, $sourceProperty, $targetProperty)) {
                        $this->addLinks($originProperty, $sourceProperty, $targetProperty);

                        return;
                    }
                    if (2 === $processType->count()) {
                        $result = [];
                        foreach ($processType->getProcesses() as $process) {
                            $result[] = $strategyInstance->confirmPropertiesMatching($process, $originProperty, $sourceProperty, $targetProperty);
                        }
                        if ($result[0] && $result[1]) {
                            $this->addLinks($originProperty, $sourceProperty, $targetProperty);

                            return;
                        }
                    }
                }
            }
        }

        throw new PropertyNotMatchedException($originProperty->getPath(), sprintf('Property "%s" from origin class "%s" could not be matched with any property from source and target classes.', $originProperty->originName, $originProperty->parent->name), sprintf('Check Your classes: origin:"%s", source:"%s" and target:"%s" for properties with the same name or with the same attributes. Use #[TargetProperty] attribute to match properties if the names cannot be same.', $originProperty->parent->name, $source->name, $target->name));
    }

    protected function addLinks(
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
}
