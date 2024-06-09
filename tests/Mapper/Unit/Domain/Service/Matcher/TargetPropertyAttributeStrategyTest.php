<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Mapper\Unit\Domain\Service\Matcher;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Mapper\Application\Attribute\TargetProperty;
use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class TargetPropertyAttributeStrategyTest extends TestCase
{
    public static function getDataForConfirmPropertiesMatchingMethod(): array
    {
        $classA = new class() {
            public $propertyA;
            public $propertyB;
            public $propertyC;
        };
        $classABlueprint = ClassBlueprint::create(get_class($classA), null);

        $classB = new class() {
            public $propertyA;
            #[TargetProperty('propertyA', TargetProperty::NORMALIZATION | TargetProperty::DENORMALIZATION | TargetProperty::MAPPING | TargetProperty::TRANSFORMATION)]
            public $propertyB;
            public $propertyC;
        };
        $classBBlueprint = ClassBlueprint::create(get_class($classB), null);

        return [
            // normalization
            [
                'processType' => TypeInterface::NORMALIZATION_PROCESS,
                'origin' => clone $classABlueprint->properties['propertyA'],
                'source' => clone $classABlueprint->properties['propertyA'],
                'target' => clone $classABlueprint->properties['propertyA'],
                'expectedResult' => false,
            ],
            [
                'processType' => TypeInterface::NORMALIZATION_PROCESS,
                'origin' => clone $classBBlueprint->properties['propertyB'],
                'source' => clone $classBBlueprint->properties['propertyB'],
                'target' => clone $classABlueprint->properties['propertyA'],
                'expectedResult' => false,
            ],
            [
                'processType' => TypeInterface::NORMALIZATION_PROCESS,
                'origin' => clone $classBBlueprint->properties['propertyA'],
                'source' => clone $classBBlueprint->properties['propertyA'],
                'target' => clone $classABlueprint->properties['propertyB'],
                'expectedResult' => false,
            ],

            // denormalization
            [
                'processType' => TypeInterface::DENORMALIZATION_PROCESS,
                'origin' => clone $classABlueprint->properties['property'],
                'source' => clone $classABlueprint->properties['property'],
                'target' => clone $classABlueprint->properties['property'],
                'expectedResult' => false,
            ],

            // mapping
            [
                'processType' => TypeInterface::MAPPING_PROCESS,
                'origin' => clone $classABlueprint->properties['property'],
                'source' => clone $classABlueprint->properties['property'],
                'target' => clone $classABlueprint->properties['property'],
                'expectedResult' => false,
            ],

            // transformation
            [
                'processType' => TypeInterface::TRANSFORMATION_PROCESS,
                'origin' => clone $classABlueprint->properties['property'],
                'source' => clone $classABlueprint->properties['property'],
                'target' => clone $classABlueprint->properties['property'],
                'expectedResult' => false,
            ],
        ];
    }
}
