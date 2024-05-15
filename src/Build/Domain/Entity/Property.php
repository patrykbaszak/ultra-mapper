<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Domain\Entity;

use PBaszak\UltraMapper\Build\Application\Model\Blueprints;

class Property
{
    public string $sourceName;
    public string $targetName;

    public bool $isCollection = false;
    public bool $isNullable = false;

    public function __construct(
        public Blueprints $blueprints,
    ) {
    }
}
