<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Modules\Extender;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

class Extender implements Contract\ExtenderInterface
{
    private int $recursionLevel = 0;

    /**
     * @param array<Contract\ExtenderStrategyInterface> $strategies
     */
    public function __construct(
        private array $strategies,
        private int $maxRecursionLevel = 100
    ) {
        $this->recursionLevel = 0;
    }

    public function extend(Blueprint $blueprint, Process $process, Context $context, string $processUse): bool
    {
        $extended = false;

        foreach ($this->strategies as $strategy) {
            $extended = $strategy->extend($blueprint, $process, $context, $processUse) || $extended;
        }

        if ($extended) {
            ++$this->recursionLevel;
            $this->extend($blueprint, $process, $context, $processUse);

            if ($this->recursionLevel > $this->maxRecursionLevel) {
                throw new Exception\ExtenderException(sprintf('Max recursion level of %d reached', $this->maxRecursionLevel), 'Extender recursion level can be increased by setting the maxRecursionLevel property in the constructor of the Extender class. The default value is 100. If you are sure that the recursion level is not a problem, you can increase the value. If you are not sure, you should check the blueprint and the strategies to see if there is a problem with the blueprint or the strategies.');
            }

            --$this->recursionLevel;
        }

        return $extended;
    }
}
