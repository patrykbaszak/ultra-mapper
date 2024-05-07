<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit\Entity;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Method;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

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
