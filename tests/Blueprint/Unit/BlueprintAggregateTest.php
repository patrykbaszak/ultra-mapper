<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\BlueprintAggregate;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class BlueprintAggregateTest extends TestCase
{
    #[Test]
    public function testCreateWithValidClass(): void
    {
        $class = Blueprint::class;
        $aggregate = BlueprintAggregate::create($class, null);

        $this->assertEquals('pbaszak_ultramapper_blueprint_domain_entity_blueprint', $aggregate->root);
        $this->assertArrayHasKey('pbaszak_ultramapper_blueprint_domain_entity_blueprint', $aggregate->blueprints);
        $this->assertArrayHasKey($aggregate->blueprints['pbaszak_ultramapper_blueprint_domain_entity_blueprint']->filePath, $aggregate->filesHashes);
        $this->assertContains('Blueprint Aggregate created. Root class: PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint.', $aggregate->events);
    }

    #[Test]
    public function testCreateWithValidDummyClass(): void
    {
        $class = Dummy::class;
        $aggregate = BlueprintAggregate::create($class, null);

        $this->assertEquals('pbaszak_ultramapper_tests_assets_dummy', $aggregate->root);
        $this->assertArrayHasKey('pbaszak_ultramapper_tests_assets_dummy', $aggregate->blueprints);
        $this->assertArrayHasKey($aggregate->blueprints['pbaszak_ultramapper_tests_assets_dummy']->filePath, $aggregate->filesHashes);
        $this->assertContains('Blueprint Aggregate created. Root class: PBaszak\UltraMapper\Tests\Assets\Dummy.', $aggregate->events);
    }
}
