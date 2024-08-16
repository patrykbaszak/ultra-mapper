<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Unit\Reflection\Domain;

use PBaszak\UltraMapper\Reflection\Domain\Reflection;
use PBaszak\UltraMapper\Tests\Support\Assets\DTO\Dummy;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class ReflectionTest extends TestCase
{
    public function testCreate(): void
    {
        $class = Dummy::class;
        $reflection = Reflection::create($class);

        self::assertSame($class, $reflection->rootClass());
    }

    public function testRecreate(): void
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
