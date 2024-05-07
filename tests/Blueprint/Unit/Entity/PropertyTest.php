<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit\Entity;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Property;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

#[Group('unit')]
class PropertyTest extends TestCase
{
    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = Dummy::class;
        $blueprint = Blueprint::create($class, null);

        $property = $blueprint->properties->properties['id'];

        $this->assertInstanceOf(Property::class, $property);
    }

    #[Test]
    public function testGetReflection(): void
    {
        $class = Dummy::class;
        $blueprint = Blueprint::create($class, null);

        $ref = $blueprint->properties->properties['id']->getReflection();

        $this->assertInstanceOf(ReflectionProperty::class, $ref);
        $this->assertEquals('id', $ref->getName());
    }
}
