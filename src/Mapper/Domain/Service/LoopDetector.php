<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Service;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Attribute\Ignore;
use PBaszak\UltraMapper\Mapper\Application\Attribute\MaxDepth;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

class LoopDetector
{
    protected Blueprint $root;

    /**
     * @param Blueprint $blueprint to check
     *
     * @throws \RuntimeException if loop is detected
     */
    public function checkBlueprint(Blueprint $blueprint, Process $process): void
    {
        $this->root = $blueprint;
        foreach ($blueprint->blueprints as $index => $classBlueprint) {
            foreach ($process->processes as $processType) {
                $this->checkClassBlueprint($classBlueprint, $classBlueprint->name, $processType);
            }
        }
    }

    protected function checkClassBlueprint(ClassBlueprint $blueprint, string $actualCheckedClass, string $processType): void
    {
        /** @var PropertyBlueprint $property */
        foreach ($blueprint->properties as $property) {
            $types = $property->type->getAllClassTypes();
            if (empty($types)) {
                continue;
            }

            foreach ($types as $classType) {
                if ($actualCheckedClass == $classType) {
                    if (
                        $this->hasAttribute($property, Ignore::class, $processType)
                        || $this->hasAttribute($property, MaxDepth::class, $processType)
                    ) {
                        continue;
                    }

                    throw new \RuntimeException('Loop detected');
                }

                foreach ($this->root->blueprints as $classBlueprint) {
                    if ($classType == $classBlueprint->name) {
                        $this->checkClassBlueprint($classBlueprint, $actualCheckedClass, $processType);
                    }
                }
            }
        }
    }

    protected function hasAttribute(PropertyBlueprint $property, string $attr, string $processType): bool
    {
        return !empty($property->attributes[$attr]);

        foreach ($property->attributes[$attr] ?? [] as $attribute) {
            /** @var MaxDepth|Ignore $instance */
            $instance = $attribute->newInstance();

            $binaryProcessType = $instance::PROCESS_TYPE_MAP[$processType];
            if (($instance->processType & $binaryProcessType) === $binaryProcessType) {
                return $instance;
            }
        }

        return null;
    }
}
