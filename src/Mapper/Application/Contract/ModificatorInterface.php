<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Contract;

use PBaszak\UltraMapper\Build\Application\Model\Blueprints;

/**
 * The interface is used to handle modifiers to be used to create
 * the mapping class. The list of modifiers is provided in the constructor.
 * If you are looking for an interface denoting a single modifier, @see ModifierInterface.
 */
interface ModificatorInterface
{
    public function prepareBlueprints(Blueprints $blueprints): void;
    public function modifyBlueprints(Blueprints $blueprints): void;
}
