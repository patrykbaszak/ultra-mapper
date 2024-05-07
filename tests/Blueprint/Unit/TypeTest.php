<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Property;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Type;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\TypeNotDeclaredException;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use ReflectionType;

#[Group('unit')]
class TypeTest extends TestCase
{
    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = Dummy::class;
        $blueprint = Blueprint::create($class, null);

        $type = $blueprint->properties->properties['id']->type;

        $this->assertInstanceOf(Type::class, $type);
    }

    #[Test]
    public function testGetReflection(): void
    {
        $class = Dummy::class;
        $blueprint = Blueprint::create($class, null);

        $ref = $blueprint->properties->properties['id']->type->getReflection();

        $this->assertInstanceOf(ReflectionType::class, $ref);
        $this->assertEquals('string', $ref->getName());
    }

    #[Test]
    public function shouldThrowExceptionWhenPropertyHasNoType(): void
    {
        $this->expectException(TypeNotDeclaredException::class);
        $this->expectExceptionCode(5941);
        $obj = new class {
            public $id;
        };

        Blueprint::create(get_class($obj), null);
    }

    #[Test]
    public function shouldThrowExceptionWhenParameterHasNoType(): void
    {
        $this->expectException(TypeNotDeclaredException::class);
        $this->expectExceptionCode(5942);
        $obj = new class('') {
            public function __construct(
                $id
            ) {}
        };

        Blueprint::create(get_class($obj), null);
    }

    #[Test]
    public function shouldNotThrowExceptionWhenParameterHasNoTypeAndItsNotConstructor(): void
    {
        $obj = new class('') {
            public function test(
                $id
            ) {}
        };

        Blueprint::create(get_class($obj), null);
        $this->assertTrue(true);
    }
}
