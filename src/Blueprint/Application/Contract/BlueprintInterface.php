<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Exception\ClassNotFoundException;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;

interface BlueprintInterface
{
    /**
     * Create a blueprint for the given class.
     *
     * @param class-string $class
     *
     * @throws ClassNotFoundException if the class does not exist
     */
    public function createBlueprint(string $class): Blueprint;

    /**
     * Save the blueprint to the yaml file.
     *
     * @return bool true if the blueprint was saved successfully, false otherwise
     */
    public function saveBlueprint(Blueprint $blueprint): bool;

    /**
     * If your application settings allow it, you can perform a process of checking whether
     * the dependent files for a given blueprint have been changed since they were last generated.
     *
     * @param class-string $blueprintClass
     *
     * @throws ClassNotFoundException if the blueprint class does not exist
     */
    public function checkIfBlueprintFilesWasChanged(string $blueprintClass): bool;
}
