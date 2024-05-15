<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Property;

class PropertyAssignment
{
    public const OPTION_INITIALIZATION_VALUE = 1;
    public const OPTION_NO_DEFAULT_VALUE = 2;
    public const OPTION_HAS_DEFAULT_VALUE = 3;
    public const OPTION_INITIALIZATION_IF_NOT_EXISTS = 4;

    public string $id;
    public Property $blueprint;

    public Property $source;
    public Property $target;

    /** @var array<string, string[]> */
    public array $callbacks;

    public int $option;
}
