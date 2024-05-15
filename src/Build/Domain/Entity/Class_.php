<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Domain\Entity;

use PBaszak\UltraMapper\Build\Application\Model\Blueprints;

class Class_
{
    public function __construct(
        public Blueprints $blueprints,
        /** @var array<string, Property> */
        public array $properties,
    ) {
    }
}
