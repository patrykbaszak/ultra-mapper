<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit;

use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\BlueprintAggregate;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class BlueprintAggregateTest extends TestCase
{
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
