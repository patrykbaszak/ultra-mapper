<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit;

use PBaszak\UltraMapper\Blueprint\Application\Enum\ClassType;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\AttributeAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\MethodAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\PropertyAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Method;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Parameter;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Property;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\BlueprintException;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\ClassNotFoundException;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

#[Group('unit')]
class MethodTest extends TestCase
{
    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = Dummy::class;
        $blueprint = Blueprint::create($class, null);

        $method = $blueprint->methods->methods['__construct'];

        $this->assertInstanceOf(Method::class, $method);
    }

    #[Test]
    public function testGetReflection(): void
    {
        $class = Dummy::class;
        $blueprint = Blueprint::create($class, null);

        $ref = $blueprint->methods->methods['__construct']->getReflection();

        $this->assertInstanceOf(ReflectionMethod::class, $ref);
        $this->assertEquals('__construct', $ref->getName());
    }
}
