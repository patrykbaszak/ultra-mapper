<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit\Application\Model;

use PBaszak\UltraMapper\Blueprint\Application\Enum\ClassType;
use PBaszak\UltraMapper\Blueprint\Application\Exception\BlueprintException;
use PBaszak\UltraMapper\Blueprint\Application\Exception\ClassNotFoundException;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\AssetsAggregate;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class ClassBlueprintTest extends TestCase
{
    #[Test]
    public function shouldThrowExceptionWhenBlueprintClassGiven(): void
    {
        $this->expectException(BlueprintException::class);
        $this->expectExceptionCode(5922);

        ClassBlueprint::create(ClassBlueprint::class, null);
    }

    #[Test]
    public function shouldThrowExceptionWhenClassNotFound(): void
    {
        $this->expectException(ClassNotFoundException::class);
        $this->expectExceptionCode(5931);

        ClassBlueprint::create('InvalidClass', null);
    }

    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = Dummy::class;
        $blueprint = ClassBlueprint::create($class, null);

        $this->assertEquals($class, $blueprint->name);
        $this->assertEquals('Dummy', $blueprint->shortName);
        $this->assertStringContainsString('PBaszak\\UltraMapper\\Tests\\Assets', $blueprint->namespace);
        $this->assertNotNull($blueprint->filePath);
        $this->assertNotNull($blueprint->hash);
        $this->assertEquals(ClassType::STANDARD, $blueprint->type);
        $this->assertInstanceOf(AssetsAggregate::class, $blueprint->attributes);
        $this->assertInstanceOf(AssetsAggregate::class, $blueprint->properties);
        $this->assertInstanceOf(AssetsAggregate::class, $blueprint->methods);
        $this->assertTrue($blueprint->hasDeclarationFile());
    }

    #[Test]
    public function testCreateWithValidAnonymousClass(): void
    {
        $class = get_class(new class() {
            public function test(): void
            {
            }
        });
        $blueprint = ClassBlueprint::create($class, null);

        $this->assertEquals($class, $blueprint->name);
        $this->assertStringContainsString('class@anonymous', $blueprint->shortName);
        $this->assertEquals('', $blueprint->namespace);
        $this->assertEquals(__FILE__, $blueprint->filePath);
        $this->assertNotNull($blueprint->hash);
        $this->assertEquals(ClassType::STANDARD, $blueprint->type);
        $this->assertInstanceOf(AssetsAggregate::class, $blueprint->attributes);
        $this->assertInstanceOf(AssetsAggregate::class, $blueprint->properties);
        $this->assertInstanceOf(AssetsAggregate::class, $blueprint->methods);
        $this->assertTrue($blueprint->hasDeclarationFile());
    }

    #[Test]
    public function testcreateWithValidClassAndAbstractClass(): void
    {
        $class = Dummy::class;
        $blueprint = ClassBlueprint::create($class, null);

        $this->assertArrayHasKey('abstractField', $blueprint->properties->assets);
    }

    #[Test]
    public function testTypeDeclarationsAreSetCorrectly(): void
    {
        $blueprint = ClassBlueprint::create(Dummy::class, null);

        $this->assertNull($blueprint->parent);
        $this->assertTrue(false === $blueprint->docBlock || is_string($blueprint->docBlock));
        $this->assertEquals(ClassType::STANDARD, $blueprint->type);
    }

    #[Test]
    public function testCreateWithInvalidClass(): void
    {
        $this->expectException(ClassNotFoundException::class);
        ClassBlueprint::create('InvalidClass', null);
    }

    #[Test]
    public function testGetReflection(): void
    {
        $blueprint = ClassBlueprint::create(Dummy::class, null);
        $reflection = $blueprint->getReflection();

        $this->assertInstanceOf(\ReflectionClass::class, $reflection);
        $this->assertEquals(Dummy::class, $reflection->getName());
    }
}
