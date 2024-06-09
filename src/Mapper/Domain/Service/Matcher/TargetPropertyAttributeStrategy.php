<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Service\Matcher;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Mapper\Application\Attribute\TargetProperty;
use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;

class TargetPropertyAttributeStrategy implements MatchingStrategyInterface
{
    public function confirmClassMatching(string $processType, ClassBlueprint $origin, ClassBlueprint $source, ClassBlueprint $target): bool
    {
        return false; // todo based on `path` argument
    }

    public function confirmPropertiesMatching(string $processType, PropertyBlueprint $origin, PropertyBlueprint $source, PropertyBlueprint $target): bool
    {
        $sourceTargetProperty = $this->getPropertyTargetPropertyAttribute($source, $processType);
        $hasSourceTargetProperty = null !== $sourceTargetProperty;
        $targetTargetProperty = $this->getPropertyTargetPropertyAttribute($target, $processType);
        $hasTargetTargetProperty = null !== $targetTargetProperty;

        // source affects source
        if ($this->isTargetPropertyAttrHasAffect($processType, 'source', 'source', $hasSourceTargetProperty, $hasTargetTargetProperty)) {
            if ($origin->originName === $source->originName && $origin->originName === $target->originName) {
                $source->options['name'] = $sourceTargetProperty->name;

                return true;
            }
        }

        // source affects target
        if ($this->isTargetPropertyAttrHasAffect($processType, 'source', 'target', $hasSourceTargetProperty, $hasTargetTargetProperty)) {
            if ($origin->originName === $source->originName && $sourceTargetProperty->name === $target->originName) {
                return true;
            }
        }

        // target affects source
        if ($this->isTargetPropertyAttrHasAffect($processType, 'target', 'source', $hasSourceTargetProperty, $hasTargetTargetProperty)) {
            if ($origin->originName === $target->originName && $targetTargetProperty->name === $source->originName) {
                return true;
            }
        }

        // target affects target
        if ($this->isTargetPropertyAttrHasAffect($processType, 'target', 'target', $hasSourceTargetProperty, $hasTargetTargetProperty)) {
            if ($origin->originName === $target->originName && $origin->originName === $source->originName) {
                $target->options['name'] = $targetTargetProperty->name;

                return true;
            }
        }

        return false;
    }

    /**
     * @param string<"source"|"target"> $declarationPlace
     * @param string<"source"|"target"> $context          (the resource which is possible affected by target property attribute)
     * @param bool                      $sourceHas        whether source has target property attribute
     * @param bool                      $targetHas        whether target has target property attribute
     */
    protected function isTargetPropertyAttrHasAffect(string $processType, string $declarationPlace, string $context, bool $sourceHas, bool $targetHas): bool
    {
        return match ($processType) {
            TypeInterface::NORMALIZATION_PROCESS => match ($declarationPlace) {
                'source' => false,
                'target' => match ($context) {
                    'source' => false,
                    'target' => $targetHas,
                }
            },
            TypeInterface::DENORMALIZATION_PROCESS => match ($declarationPlace) {
                'source' => match ($context) {
                    'source' => $sourceHas,
                    'target' => false,
                },
                'target' => false,
            },
            TypeInterface::MAPPING_PROCESS => match ($declarationPlace) {
                'source' => match ($context) {
                    'source' => false,
                    'target' => $targetHas,
                },
                'target' => match ($context) {
                    'source' => $sourceHas,
                    'target' => false,
                }
            },
            TypeInterface::TRANSFORMATION_PROCESS => match ($declarationPlace) {
                'source' => match ($context) {
                    'source' => false,
                    'target' => $targetHas,
                },
                'target' => match ($context) {
                    'source' => $sourceHas,
                    'target' => false,
                }
            },
        };
    }

    protected function getPropertyTargetPropertyAttribute(PropertyBlueprint $blueprint, string $processType): ?TargetProperty
    {
        foreach ($blueprint->attributes[TargetProperty::class] ?? [] as $attribute) {
            /** @var TargetProperty $instance */
            $instance = $attribute->newInstance();

            $binaryProcessType = $instance::PROCESS_TYPE_MAP[$processType];
            if (($instance->useNameFor & $binaryProcessType) === $binaryProcessType) {
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
            if (($instance->usePathFor & $binaryProcessType) === $binaryProcessType) {
                return $instance;
            }
        }

        return null;
    }
}
