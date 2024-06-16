<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Mapper\Unit\Domain\Service;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;
use PBaszak\UltraMapper\Mapper\Domain\Service\LoopDetector;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class LoopDetectorTest extends TestCase
{
    #[Test]
    public function shouldThrowExceptionOnLoopDetected(): void
    {
        $class = new class() {
            public self $property;
        };

        $this->expectException(\RuntimeException::class);
        (new LoopDetector())->checkBlueprint(
            Blueprint::create(get_class($class)),
            new Process([Process::DENORMALIZATION_PROCESS])
        );
    }
}
