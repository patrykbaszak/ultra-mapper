<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;

class ClassAssignment
{
    public string $id;
    public Blueprint $blueprint;

    public Blueprint $source;
    public Blueprint $target;

    /** @var array<string, PropertyAssignment> */
    public array $propertyAssignments = [];
}
