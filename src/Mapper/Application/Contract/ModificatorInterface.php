<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

/**
 * The interface is used to handle modifiers to be used to create
 * the mapping class. The list of modifiers is provided in the constructor.
 * If you are looking for an interface denoting a single modifier, @see ModifierInterface.
 */
interface ModificatorInterface
{
    /**
     * Get the list of modifiers. It's required by MapperService
     * to create hash of mapping class unique for the given modifiers.
     *
     * @return ModifierInterface[]
     */
    public function getModifiers(): array;

    /**
     * Prepare the blueprint for the mapping class. Called before
     * matching properties.
     *
     * @param string<"origin"|"source"|"target"> $processUse
     */
    public function prepareBlueprint(Blueprint $blueprint, Process $processType, string $processUse): void;

    public function modifyBlueprints(): void;
}
