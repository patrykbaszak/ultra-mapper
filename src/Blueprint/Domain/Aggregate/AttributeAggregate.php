<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Aggregate;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Attribute;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;

class AttributeAggregate
{
    public function __construct(
        public Blueprint $root,
        /** @var array<string, Attribute> $attributes */
        public array $attributes,
    ) {
    }

    public static function create(Blueprint $root): self
    {
        $ref = $root->getReflection();

        $properties = [];
        foreach ($ref->getAttributes() as $attribute) {
            $properties[$attribute->getName()] = Attribute::create($attribute, $root);
        }

        return new self($root, $properties);
    }
}
