<?php

declare(strict_types=1);

use PBaszak\UltraMapper\Blueprint\Application\Enum\ClassType;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class BlueprintTest extends TestCase
{
    #[Test]
    public function testCreateWithValidClass(): void
    {
        $class = Blueprint::class;
        $blueprint = Blueprint::create($class, null);

        $this->assertInstanceOf(Blueprint::class, $blueprint);
        $this->assertEquals($class, $blueprint->name);
        $this->assertEquals('Blueprint', $blueprint->shortName);
        $this->assertStringContainsString('PBaszak\\UltraMapper\\Blueprint\\Domain\\Entity', $blueprint->namespace);
        $this->assertNotNull($blueprint->filePath);
        $this->assertNotNull($blueprint->fileHash);
        $this->assertEquals(ClassType::STANDARD, $blueprint->type);
        $this->assertIsArray($blueprint->attributes);
        $this->assertIsArray($blueprint->properties);
        $this->assertIsArray($blueprint->methods);
    }

    #[Test]
    public function testPropertyTypesAreSetCorrectly(): void
    {
        $blueprint = Blueprint::create(Blueprint::class, null);

        $this->assertNull($blueprint->parent);
        $this->assertTrue(false === $blueprint->docBlock || is_string($blueprint->docBlock));
        $this->assertEquals(ClassType::STANDARD, $blueprint->type);
    }

    #[Test]
    public function testCreateWithInvalidClass(): void
    {
        $this->expectException(ReflectionException::class);
        Blueprint::create('InvalidClass', null);
    }

    #[Test]
    public function testGetReflection(): void
    {
        $blueprint = Blueprint::create(Blueprint::class, null);
        $reflection = $blueprint->getReflection();

        $this->assertInstanceOf(ReflectionClass::class, $reflection);
        $this->assertEquals(Blueprint::class, $reflection->getName());
    }
}
