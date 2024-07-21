<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Mapper\Unit\Domain\Modules\Checker;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Attribute\Groups;
use PBaszak\UltraMapper\Mapper\Application\Attribute\Ignore;
use PBaszak\UltraMapper\Mapper\Application\Attribute\MaxDepth;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;
use PBaszak\UltraMapper\Mapper\Domain\Modules\Checker\Exception\CheckerException;
use PBaszak\UltraMapper\Mapper\Domain\Modules\Checker\Strategy\RecursiveLoopChecker;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class RecursiveLoopCheckerTest extends TestCase
{
    #[Test]
    public function shouldThrowExceptionOnLoopDetected(): void
    {
        $class = new class() {
            public self $property;
        };

        $this->expectException(CheckerException::class);
        (new RecursiveLoopChecker())->check(
            Blueprint::create(get_class($class)),
            new Process([Process::DENORMALIZATION_PROCESS]),
            new Context()
        );
    }

    #[Test]
    public function shouldThrowExceptionOnLoopDetectedInTheGroupContext(): void
    {
        $class = new class() {
            #[Groups('test')]
            public self $property;
        };

        $this->expectException(CheckerException::class);
        (new RecursiveLoopChecker())->check(
            Blueprint::create(get_class($class)),
            new Process([Process::DENORMALIZATION_PROCESS]),
            new Context(['test'])
        );
    }

    #[Test]
    public function shouldNotThrowExceptionBecauseOfIgnoreAttribute(): void
    {
        $class = new class() {
            #[Ignore()]
            public self $property;
        };

        (new RecursiveLoopChecker())->check(
            Blueprint::create(get_class($class)),
            new Process([Process::DENORMALIZATION_PROCESS]),
            new Context()
        );

        $this->expectNotToPerformAssertions();
    }

    #[Test]
    public function shouldNotThrowExceptionBecauseOfMaxDepthAttribute(): void
    {
        $class = new class() {
            #[MaxDepth(1)]
            public self $property;
        };

        (new RecursiveLoopChecker())->check(
            Blueprint::create(get_class($class)),
            new Process([Process::DENORMALIZATION_PROCESS]),
            new Context()
        );

        $this->expectNotToPerformAssertions();
    }
}
