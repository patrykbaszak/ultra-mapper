<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Service\Matcher;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Mapper\Application\Attribute\TargetProperty;

class TargetPropertyAttributeStrategy implements MatchingStrategyInterface
{
    public function confirmClassMatching(string $processType, ClassBlueprint $origin, ClassBlueprint $source, ClassBlueprint $target): bool
    {
        return false; // todo based on `path` argument
    }

    public function confirmPropertiesMatching(string $processType, PropertyBlueprint $origin, PropertyBlueprint $source, PropertyBlueprint $target): bool
    {
        if ($sourceTargetProperty = $this->getPropertyTargetPropertyAttribute($source, $processType)) {
            // source has same name as origin, source has target property attribute
            if ($origin->originName === $source->originName && $target->originName === $sourceTargetProperty->name) {
                $target->options['name'] ??= $sourceTargetProperty->name;

                return true;
            }
        }

        if ($originTargetProperty = $this->getPropertyTargetPropertyAttribute($origin, $processType)) {
            // source has same name as origin, but the origin has target property attribute
            if ($origin->originName === $source->originName && $source->originName === $originTargetProperty->name) {
                $target->options['name'] ??= $originTargetProperty->name;

                return true;
            }
        }

        if ($targetTargetProperty = $this->getPropertyTargetPropertyAttribute($target, $processType)) {
            // target has same name as origin, target has target property attribute
            if ($origin->originName === $target->originName && $source->originName === $targetTargetProperty->name) {
                $source->options['name'] ??= $targetTargetProperty->name;

                return true;
            }

            // target has same name as source, but the target has target property attribute
            if ($source->originName === $target->originName && $origin->originName === $targetTargetProperty->name) {
                // do nothing, source and target are already matched, only origin has different originName but it's match
                // based on target property attribute with both source and target

                return true;
            }
        }

        return false;
    }

    protected function getPropertyTargetPropertyAttribute(PropertyBlueprint $blueprint, string $processType): ?TargetProperty
    {
        foreach ($blueprint->attributes[TargetProperty::class] as $attribute) {
            /** @var TargetProperty $instance */
            $instance = $attribute->newInstance();

            $binaryProcessType = $instance::PROCESS_TYPE_MAP[$processType];
            if ($instance->useNameFor & $binaryProcessType === $binaryProcessType) {
                return $instance;
            }
        }

        return null;
    }

    protected function getClassTargetPropertyAttribute(ClassBlueprint $blueprint, string $processType): ?TargetProperty
    {
        if (!$blueprint->parent) {
            return null;
        }

        foreach ($blueprint->parent->attributes[TargetProperty::class] as $attribute) {
            /** @var TargetProperty $instance */
            $instance = $attribute->newInstance();

            $binaryProcessType = $instance::PROCESS_TYPE_MAP[$processType];
            if ($instance->usePathFor & $binaryProcessType === $binaryProcessType) {
                return $instance;
            }
        }

        return null;
    }
}
