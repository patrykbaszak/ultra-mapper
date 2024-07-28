<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit\Application\Model;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

#[Group('unit')]
class PropertyBlueprintTest extends TestCase
{
    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = Dummy::class;
        $blueprint = ClassBlueprint::create($class, null);

        $property = $blueprint->properties->assets['id'];

        $this->assertInstanceOf(PropertyBlueprint::class, $property);
    }

    #[Test]
    public function testGetReflection(): void
    {
        $class = Dummy::class;
        $blueprint = ClassBlueprint::create($class, null);

        $ref = $blueprint->properties->assets['id']->getReflection();

        $this->assertInstanceOf(ReflectionProperty::class, $ref);
        $this->assertEquals('id', $ref->getName());
    }
}
