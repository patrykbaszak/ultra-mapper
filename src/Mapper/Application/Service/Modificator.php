<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Service;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Contract\ModificatorInterface;
use PBaszak\UltraMapper\Mapper\Application\Contract\ModifierInterface;

class Modificator implements ModificatorInterface
{
    public function __construct(
        /** @var ModifierInterface[] */
        protected array $modifiers = []
    ) {
    }

    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    public function prepareBlueprint(Blueprint $blueprint, string $processType, string $processUse): void
    {
        foreach ($this->modifiers as $modifier) {
            // $modifier->prepareBlueprints();
        }
    }

    public function modifyBlueprints(): void
    {
        foreach ($this->modifiers as $modifier) {
            // $modifier->modifyBlueprints();
        }
    }
}
