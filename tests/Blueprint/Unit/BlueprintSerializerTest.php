<?php

declare(strict_types=1);

use PBaszak\UltraMapper\Blueprint\Application\Enum\ClassType;
use PBaszak\UltraMapper\Blueprint\Application\Serializer\BlueprintSerializer;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class BlueprintSerializerTest extends TestCase
{
    private const PATH = __DIR__.'/../../../var/ultra-mapper/blueprints';
    private const BLUEPRINT_NAME = 'pbaszak_ultramapper_blueprint_domain_entity_blueprint';

    #[Test]
    public function testSerialize(): void
    {
        if (file_exists(self::PATH.'/'.self::BLUEPRINT_NAME.'.yaml')) {
            unlink(self::PATH.'/'.self::BLUEPRINT_NAME.'.yaml');
        }
        $blueprint = Blueprint::create(Blueprint::class, null);
        $serializer = new BlueprintSerializer(self::PATH);

        $serializer->serialize($blueprint);

        $this->assertFileExists(self::PATH.'/'.self::BLUEPRINT_NAME.'.yaml');
    }

    #[Test]
    #[Depends('testSerialize')]
    public function testDeserialize(): void
    {
        $serializer = new BlueprintSerializer(self::PATH);
        $blueprint = $serializer->deserialize(self::BLUEPRINT_NAME);

        $this->assertInstanceOf(Blueprint::class, $blueprint);
        $this->assertEquals(Blueprint::class, $blueprint->name);
        $this->assertEquals('Blueprint', $blueprint->shortName);
        $this->assertStringContainsString('PBaszak\\UltraMapper\\Blueprint\\Domain\\Entity', $blueprint->namespace);
        $this->assertNotNull($blueprint->filePath);
        $this->assertNotNull($blueprint->fileHash);
        $this->assertEquals(ClassType::STANDARD, $blueprint->type);
        $this->assertIsArray($blueprint->attributes);
        $this->assertIsArray($blueprint->properties);
        $this->assertIsArray($blueprint->methods);
    }
}
