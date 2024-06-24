<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Matcher\Service;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

class DiscriminatorService
{
    protected Blueprint $root;

    /**
     * @param Blueprint $blueprint to check
     *
     * @throws \RuntimeException if loop is detected
     */
    public function checkBlueprint(Blueprint $blueprint, Process $process): void
    {
        $this->root = $blueprint;
        foreach ($blueprint->blueprints as $index => $classBlueprint) {
            foreach ($process->processes as $processType) {
                // $this->checkClassBlueprint($classBlueprint, $classBlueprint->name, $processType);
            }
        }
    }
}
