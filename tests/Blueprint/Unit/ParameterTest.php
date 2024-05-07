<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Parameter;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\BlueprintException;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

#[Group('unit')]
class ParameterTest extends TestCase
{
    #[Test]
    public function shouldThrowExceptionWhenParamDoesNotExistsInMethod(): void
    {
        $class = Dummy::class;
        $blueprint = Blueprint::create($class, null);

        $param = $blueprint->methods->methods['__construct']->parameters->parameters['id'];
        $notExistsParam = clone $param; $notExistsParam->name = 'notExists';

        $this->expectException(BlueprintException::class);
        $this->expectExceptionCode(5923);

        $notExistsParam->getReflection();
    }

    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = Dummy::class;
        $blueprint = Blueprint::create($class, null);

        $param = $blueprint->methods->methods['__construct']->parameters->parameters['id'];

        $this->assertInstanceOf(Parameter::class, $param);
    }

    #[Test]
    public function testGetReflection(): void
    {
        $class = Dummy::class;
        $blueprint = Blueprint::create($class, null);

        $ref = $blueprint->methods->methods['__construct']->parameters->parameters['id']->getReflection();

        $this->assertInstanceOf(ReflectionParameter::class, $ref);
        $this->assertEquals('id', $ref->getName());
    }
}
