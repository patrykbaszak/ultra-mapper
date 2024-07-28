<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Modules\Modificator;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

class Modificator implements Contract\ModificatorInterface
{
    public function __construct(
        /** @var Contract\ModifierInterface[] */
        private array $modifiers = []
    ) {
    }

    /**
     * @return Contract\ModifierInterface[]
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    public function modify(Blueprint $blueprint, Process $process, Context $context, string $processUse): bool
    {
        $modified = false;

        foreach ($this->modifiers as $modifier) {
            // $modified = $modifier->modify($blueprint, $process, $context, $processUse) || $modified;
        }

        return $modified;
    }
}
