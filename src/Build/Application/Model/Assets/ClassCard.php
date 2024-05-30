<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Model\Assets;

class ClassCard
{
    public ConstructorCard $constructor;
    /** @var PropertyCard[] */
    public array $properties;
}
