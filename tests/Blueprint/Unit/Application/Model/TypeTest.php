<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit\Application\Model;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Type;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionType;

#[Group('unit')]
class TypeTest extends TestCase
{
    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = Dummy::class;
        $blueprint = ClassBlueprint::create($class, null);

        $type = $blueprint->properties->assets['id']->type;

        $this->assertInstanceOf(Type::class, $type);
    }

    #[Test]
    public function testGetReflection(): void
    {
        $class = Dummy::class;
        $blueprint = ClassBlueprint::create($class, null);

        $ref = $blueprint->properties->assets['id']->type->getReflection();

        $this->assertInstanceOf(ReflectionType::class, $ref);
        $this->assertEquals('string', $ref->getName());
    }

    #[Test]
    public function shouldNotThrowExceptionWhenPropertyHasNoTypeAndTheTypeShouldBeMixed(): void
    {
        $obj = new class {
            public $id;
        };

        $types = ClassBlueprint::create(get_class($obj), null)->properties->assets['id']->type->types;
        $this->assertEquals(['mixed'], $types);
    }

    #[Test]
    public function shouldNotThrowExceptionWhenParameterHasNoTypeAndTheTypeShouldBeMixed(): void
    {
        $obj = new class('') {
            public function __construct(
                $id
            ) {}
        };

        $types = ClassBlueprint::create(get_class($obj), null)->methods->assets['__construct']->parameters->assets['id']->type->types;
        $this->assertEquals(['mixed'], $types);
    }

    #[Test]
    public function shouldNotThrowExceptionWhenParameterHasNoTypeAndItsNotConstructor(): void
    {
        $obj = new class('') {
            public function test(
                $id
            ) {}
        };

        ClassBlueprint::create(get_class($obj), null);
        $this->assertTrue(true);
    }
}
