<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Mapper\Unit\Domain\Service;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Mapper\Application\Attribute\TargetProperty;
use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;
use PBaszak\UltraMapper\Mapper\Domain\Exception\PropertyNotMatchedException;
use PBaszak\UltraMapper\Mapper\Domain\Service\Matcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class MatcherTest extends TestCase
{
    public static function getPropertyTestCases(): array
    {
        $classA = new class() {
            public string $propertyA;
            public string $propertyB;
        };
        $classABlueprint = ClassBlueprint::create(get_class($classA), null);

        $classB = new class() {
            public string $propertyA;
            #[TargetProperty('propertyF', TargetProperty::TRANSFORMATION)]
            public string $propertyD;
        };
        $classBBlueprint = ClassBlueprint::create(get_class($classB), null);

        $classC = new class() {
            public string $propertyA;
            public string $propertyF;
        };
        $classCBlueprint = ClassBlueprint::create(get_class($classC), null);

        try {
            return [
                'aaa' => [
                    TypeInterface::DENORMALIZATION_PROCESS,
                    clone $classABlueprint->properties['propertyA'],
                    clone $classABlueprint,
                    clone $classABlueprint,
                    true,
                ],
                'aba' => [
                    TypeInterface::MAPPING_PROCESS,
                    clone $classABlueprint->properties['propertyA'],
                    clone $classBBlueprint,
                    clone $classABlueprint,
                    true,
                ],
                'abb' => [
                    TypeInterface::MAPPING_PROCESS,
                    clone $classABlueprint->properties['propertyA'],
                    clone $classBBlueprint,
                    clone $classBBlueprint,
                    true,
                ],
                'abc' => [
                    TypeInterface::MAPPING_PROCESS,
                    clone $classABlueprint->properties['propertyA'],
                    clone $classBBlueprint,
                    clone $classCBlueprint,
                    true,
                ],
                'aba_fail' => [
                    TypeInterface::MAPPING_PROCESS,
                    clone $classABlueprint->properties['propertyB'],
                    clone $classBBlueprint,
                    clone $classABlueprint,
                    false,
                ],
                'abb_fail' => [
                    TypeInterface::MAPPING_PROCESS,
                    clone $classABlueprint->properties['propertyB'],
                    clone $classBBlueprint,
                    clone $classBBlueprint,
                    false,
                ],
                'abc_fail' => [
                    TypeInterface::MAPPING_PROCESS,
                    clone $classABlueprint->properties['propertyB'],
                    clone $classBBlueprint,
                    clone $classCBlueprint,
                    false,
                ],
                'bbb' => [
                    TypeInterface::MAPPING_PROCESS,
                    clone $classBBlueprint->properties['propertyD'],
                    clone $classBBlueprint,
                    clone $classBBlueprint,
                    true,
                ],
                'bbc_fail' => [
                    TypeInterface::MAPPING_PROCESS,
                    clone $classBBlueprint->properties['propertyD'],
                    clone $classBBlueprint,
                    clone $classCBlueprint,
                    false,
                ],
                'bbc' => [
                    TypeInterface::TRANSFORMATION_PROCESS,
                    clone $classBBlueprint->properties['propertyD'],
                    clone $classBBlueprint,
                    clone $classCBlueprint,
                    true,
                ],
            ];
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    #[Test]
    #[DataProvider('getPropertyTestCases')]
    public function shouldMatchProperties(string $processType, PropertyBlueprint $originProperty, ClassBlueprint $source, ClassBlueprint $target, bool $success): void
    {
        if (!$success) {
            $this->expectException(PropertyNotMatchedException::class);
        }

        $matcher = new Matcher();
        (new \ReflectionMethod($matcher, 'matchProperties'))->invoke($matcher, $processType, $originProperty, $source, $target);

        $this->assertNotEmpty($originProperty->options[$matcher::OPTION_ID]);
        $this->assertInstanceOf(PropertyBlueprint::class, $originProperty->options[$matcher::OPTION_ORIGIN]);
        $this->assertInstanceOf(PropertyBlueprint::class, $originProperty->options[$matcher::OPTION_SOURCE]);
        $this->assertInstanceOf(PropertyBlueprint::class, $originProperty->options[$matcher::OPTION_TARGET]);
        $this->assertTrue(
            $originProperty->options[$matcher::OPTION_ORIGIN] !== $originProperty->options[$matcher::OPTION_SOURCE]
                && $originProperty->options[$matcher::OPTION_ORIGIN] !== $originProperty->options[$matcher::OPTION_TARGET]
                && $originProperty->options[$matcher::OPTION_SOURCE] !== $originProperty->options[$matcher::OPTION_TARGET]
        );
    }
}
