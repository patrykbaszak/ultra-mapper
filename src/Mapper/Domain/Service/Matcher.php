<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Service;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ParameterBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Attribute\TargetProperty;
use PBaszak\UltraMapper\Mapper\Domain\Contract\MatcherInterface;
use Symfony\Component\Uid\Uuid;

class Matcher implements MatcherInterface
{
    public function matchBlueprints(string $processType, Blueprint $origin, Blueprint $source, Blueprint $target): void
    {
        $this->addLinks($origin, $source, $target);
        $rootBlueprints = array_map(
            fn(Blueprint $blueprint) => $blueprint->blueprints[$blueprint->root],
            [$origin, $source, $target]
        );
        $this->matchClassBlueprints($processType, ...$rootBlueprints);
    }

    public function matchClassBlueprints(string $processType, ClassBlueprint $originClass, ClassBlueprint $source, ClassBlueprint $target): void
    {
        $this->addLinks($originClass, $source, $target);

        foreach ($originClass->properties->assets as $property) {
            $this->matchProperties($processType, $property, $source, $target);
        }
    }

    public function matchProperties(string $processType, PropertyBlueprint $originProperty, ClassBlueprint $source, ClassBlueprint $target): void
    {

    }

    protected function searchForPropertyWithSameName(PropertyBlueprint $originProperty, ClassBlueprint $blueprint): ?PropertyBlueprint
    {
        foreach ($blueprint->properties->assets as $property) {
            if ($property->originName === $originProperty->originName) {
                return $property;
            }
        }

        return null;
    }

    protected function searchForPropertyBasedOnTargetPropertyAttribute(PropertyBlueprint $originProperty, ClassBlueprint $blueprint, ): ?PropertyBlueprint
    {
        return null;
    }

    protected function hasTargetPropertyAttribute(PropertyBlueprint|ParameterBlueprint $blueprint): bool
    {
        return isset($blueprint->attributes->assets[TargetProperty::class]) && count($blueprint->attributes->assets[TargetProperty::class]) > 0;
    }

    protected function addLinks(object $origin, object $source, object $target): void
    {
        array_walk(
            [$origin, $source, $target],
            function (object $object) {
                $object->options[self::OPTION_ID] = Uuid::v4()->toRfc4122();
            }
        );

        array_walk(
            [$origin, $source, $target],
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
