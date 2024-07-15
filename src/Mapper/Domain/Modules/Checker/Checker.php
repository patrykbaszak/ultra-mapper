<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Modules\Checker;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

class Checker implements Contract\CheckerInterface
{
    /**
     * @param array<Contract\CheckerStrategyInterface> $strategies
     */
    public function __construct(
        private array $strategies
    ) {
    }

    public function check(Blueprint $blueprint, Process $process, Context $context): void
    {
        foreach ($this->strategies as $strategy) {
            $strategy->check($blueprint, $process, $context);
        }
    }
}
