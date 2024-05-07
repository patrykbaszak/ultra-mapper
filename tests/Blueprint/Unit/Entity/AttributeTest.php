<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit\Entity;

use PBaszak\UltraMapper\Attribute\Callback;
use PBaszak\UltraMapper\Attribute\Ignore;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Attribute;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\BlueprintException;
use PBaszak\UltraMapper\Tests\Assets\DummySimpleWithAttribute;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionAttribute;

#[Group('unit')]
class AttributeTest extends TestCase
{
    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = DummySimpleWithAttribute::class;
        $blueprint = Blueprint::create($class, null);

        $attribute = $blueprint->properties->properties['id']->attributes->attributes[Callback::class][0];

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals(Callback::class, $attribute->class);
        $this->assertTrue($attribute->isPropertyAttribute());
        $this->assertFalse($attribute->isBlueprintAttribute());
    }

    #[Test]
    public function testGetReflection(): void
    {
        $class = DummySimpleWithAttribute::class;
        $blueprint = Blueprint::create($class, null);

        $ref = $blueprint->properties->properties['description']->attributes->attributes[Ignore::class][0]->getReflection();

        $this->assertInstanceOf(ReflectionAttribute::class, $ref);
        $this->assertEquals(Ignore::class, $ref->getName());
    }

    #[Test]
    public function testGetReflectionCorrectAttribute(): void
    {
        $class = DummySimpleWithAttribute::class;
        $blueprint = Blueprint::create($class, null);

        $attr = $blueprint->properties->properties['id']->attributes->attributes[Callback::class][1];
        $ref = $attr->getReflection();

        $this->assertEquals(['test2'], $ref->getArguments());
    }

    #[Test]
    public function shouldThrowExceptionWhenReflectionAttributeNotFound(): void
    {
        $class = DummySimpleWithAttribute::class;
        $blueprint = Blueprint::create($class, null);

        $attribute = $blueprint->properties->properties['id']->attributes->attributes[Callback::class][0];
        $attribute->class = 'NotExistsAttribute';

        $this->expectException(BlueprintException::class);
        $this->expectExceptionCode(5921);

        $attribute->getReflection();
    }

    #[Test]
    public function shouldThrowExceptionWhenReflectionAttributeNotFoundCauseArguments(): void
    {
        $class = DummySimpleWithAttribute::class;
        $blueprint = Blueprint::create($class, null);

        $attribute = $blueprint->properties->properties['id']->attributes->attributes[Callback::class][0];
        $attribute->arguments = ['key' => 'value'];

        $this->expectException(BlueprintException::class);
        $this->expectExceptionCode(5924);

        $attribute->getReflection();
    }
}
