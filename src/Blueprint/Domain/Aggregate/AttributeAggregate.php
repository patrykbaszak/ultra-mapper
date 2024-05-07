<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Aggregate;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Attribute;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Property;
use PBaszak\UltraMapper\Blueprint\Domain\Normalizer\Normalizable;

class AttributeAggregate implements Normalizable
{
    public function __construct(
        public Blueprint|Property $root,
        /** @var array<string, Attribute[]> $attributes */
        public array $attributes,
    ) {
    }

    public static function create(Blueprint|Property $root): self
    {
        $ref = $root->getReflection();

        $attributes = [];
        foreach ($ref->getAttributes() as $attribute) {
            $attributes[$attribute->getName()][] = Attribute::create($attribute, $root);
        }

        return new self($root, $attributes);
    }

    public function normalize(): array
    {
        return array_map(
            fn (array $attributes) => array_map(
                fn (Normalizable&Attribute $attr) => $attr->normalize(),
                $attributes
            ),
            $this->attributes
        );
    }
}
