<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit\Application\Model;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ParameterBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Exception\BlueprintException;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

#[Group('unit')]
class ParameterBlueprintTest extends TestCase
{
    #[Test]
    public function shouldThrowExceptionWhenParamDoesNotExistsInMethod(): void
    {
        $class = Dummy::class;
        $blueprint = ClassBlueprint::create($class, null);

        $param = $blueprint->methods->assets['__construct']->parameters->assets['id'];
        $notExistsParam = clone $param; $notExistsParam->name = 'notExists';

        $this->expectException(BlueprintException::class);
        $this->expectExceptionCode(5923);

        $notExistsParam->getReflection();
    }

    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = Dummy::class;
        $blueprint = ClassBlueprint::create($class, null);

        $param = $blueprint->methods->assets['__construct']->parameters->assets['id'];

        $this->assertInstanceOf(ParameterBlueprint::class, $param);
    }

    #[Test]
    public function testGetReflection(): void
    {
        $class = Dummy::class;
        $blueprint = ClassBlueprint::create($class, null);

        $ref = $blueprint->methods->assets['__construct']->parameters->assets['id']->getReflection();

        $this->assertInstanceOf(ReflectionParameter::class, $ref);
        $this->assertEquals('id', $ref->getName());
    }
}
