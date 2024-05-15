<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit\Application\Model;

use PBaszak\UltraMapper\Mapper\Application\Attribute\Callback;
use PBaszak\UltraMapper\Mapper\Application\Attribute\Ignore;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\AttributeBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Exception\BlueprintException;
use PBaszak\UltraMapper\Tests\Assets\DummySimpleWithAttribute;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionAttribute;

#[Group('unit')]
class AttributeBlueprintTest extends TestCase
{
    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = DummySimpleWithAttribute::class;
        $blueprint = ClassBlueprint::create($class, null);

        $attribute = $blueprint->properties->assets['id']->attributes->assets[Callback::class][0];

        $this->assertInstanceOf(AttributeBlueprint::class, $attribute);
        $this->assertEquals(Callback::class, $attribute->class);
        $this->assertTrue($attribute->isPropertyAttribute());
        $this->assertFalse($attribute->isBlueprintAttribute());
    }

    #[Test]
    public function testGetReflection(): void
    {
        $class = DummySimpleWithAttribute::class;
        $blueprint = ClassBlueprint::create($class, null);

        $ref = $blueprint->properties->assets['description']->attributes->assets[Ignore::class][0]->getReflection();

        $this->assertInstanceOf(ReflectionAttribute::class, $ref);
        $this->assertEquals(Ignore::class, $ref->getName());
    }

    #[Test]
    public function testGetReflectionCorrectAttribute(): void
    {
        $class = DummySimpleWithAttribute::class;
        $blueprint = ClassBlueprint::create($class, null);

        $attr = $blueprint->properties->assets['id']->attributes->assets[Callback::class][1];
        $ref = $attr->getReflection();

        $this->assertEquals(['test2'], $ref->getArguments());
    }

    #[Test]
    public function shouldThrowExceptionWhenReflectionAttributeNotFound(): void
    {
        $class = DummySimpleWithAttribute::class;
        $blueprint = ClassBlueprint::create($class, null);

        $attribute = $blueprint->properties->assets['id']->attributes->assets[Callback::class][0];
        $attribute->class = 'NotExistsAttribute';

        $this->expectException(BlueprintException::class);
        $this->expectExceptionCode(5921);

        $attribute->getReflection();
    }

    #[Test]
    public function shouldThrowExceptionWhenReflectionAttributeNotFoundCauseArguments(): void
    {
        $class = DummySimpleWithAttribute::class;
        $blueprint = ClassBlueprint::create($class, null);

        $attribute = $blueprint->properties->assets['id']->attributes->assets[Callback::class][0];
        $attribute->arguments = ['key' => 'value'];

        $this->expectException(BlueprintException::class);
        $this->expectExceptionCode(5924);

        $attribute->getReflection();
    }
}
