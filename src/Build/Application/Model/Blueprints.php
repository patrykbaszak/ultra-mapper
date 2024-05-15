<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Model;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;

class Blueprints
{
    public function __construct(
        public Blueprint|ClassBlueprint|PropertyBlueprint $origin,
        public Blueprint|ClassBlueprint|PropertyBlueprint $source,
        public Blueprint|ClassBlueprint|PropertyBlueprint $target,
    ) {
    }
}
