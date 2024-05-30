<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Model\Assets;

class PropertyCard
{
    public string $name;
    public string $path;
    // public bool $isPublic;
    // public bool $isStatic;
    // public bool $isNullable;
    // public bool $isCollection;
    // public bool $hasDefaultValue;
    // public mixed $defaultValue;
    public ?ParameterCard $parameter;
}
