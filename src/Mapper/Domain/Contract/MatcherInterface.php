<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;

interface MatcherInterface
{
    public const OPTION_ID = 'id';
    public const OPTION_PATH = 'path';
    public const OPTION_MIRROR = 'mirror';
    public const OPTION_ORIGIN = 'origin';
    public const OPTION_SOURCE = 'source';
    public const OPTION_TARGET = 'target';

    /**
     * @param string<"normalization"|"denormalization"|"transformation"|"mapping"> $processType
     */
    public function matchBlueprints(string $processType, Blueprint $origin, Blueprint $source, Blueprint $target): void;
    
    // /**
    //  * @param string<"normalization"|"denormalization"|"transformation"|"mapping"> $processType
    //  */
    // public function matchClassBlueprints(string $processType, ClassBlueprint $originClass, Blueprint $source, Blueprint $target): void;

    // /**
    //  * @param string<"normalization"|"denormalization"|"transformation"|"mapping"> $processType
    //  */
    // public function matchProperties(string $processType, PropertyBlueprint $originProperty, Blueprint $source, Blueprint $target): void;
}
