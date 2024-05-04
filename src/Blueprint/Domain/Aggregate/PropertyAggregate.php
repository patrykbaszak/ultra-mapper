<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Aggregate;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Property;

class PropertyAggregate
{
    public function __construct(
        public Blueprint $root,
        /** @var array<string, Property> $properties */
        public array $properties,
    ) {
    }

    public static function create(Blueprint $root): self
    {
        $ref = $root->getReflection();

        $properties = [];
        foreach ($ref->getProperties() as $property) {
            $properties[$property->getName()] = Property::create($property, $root);
        }

        return new self($root, $properties);
    }
}
