<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit\Application\Model;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\MethodBlueprint;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

#[Group('unit')]
class MethodBlueprintTest extends TestCase
{
    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = Dummy::class;
        $blueprint = ClassBlueprint::create($class, null);

        $method = $blueprint->methods->assets['__construct'];

        $this->assertInstanceOf(MethodBlueprint::class, $method);
    }

    #[Test]
    public function testGetReflection(): void
    {
        $class = Dummy::class;
        $blueprint = ClassBlueprint::create($class, null);

        $ref = $blueprint->methods->assets['__construct']->getReflection();

        $this->assertInstanceOf(ReflectionMethod::class, $ref);
        $this->assertEquals('__construct', $ref->getName());
    }
}
