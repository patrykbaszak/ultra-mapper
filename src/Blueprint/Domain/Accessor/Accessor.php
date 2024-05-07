<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Accessor;

use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\BlueprintAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Attribute;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Method;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Parameter;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Property;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Type;

class Accessor
{
    /** @var class-string */
    private string $type;

    public function __construct(
        private Attribute|Blueprint|Method|Parameter|Property|Type $entity,
    ) {
        $this->type = match (true) {
            $this->entity instanceof Attribute => Attribute::class,
            $this->entity instanceof Blueprint => Blueprint::class,
            $this->entity instanceof Method => Method::class,
            $this->entity instanceof Parameter => Parameter::class,
            $this->entity instanceof Property => Property::class,
            $this->entity instanceof Type => Type::class,
        };
    }

    public function getBlueprintAggregate(): ?BlueprintAggregate
    {
        return match ($this->type) {
            Attribute::class => $this->entity->parent instanceof Blueprint
                ? $this->entity->parent->aggregate
                : $this->entity->parent->parent->aggregate,
            Blueprint::class => $this->entity->aggregate,
            Method::class => $this->entity->parent->aggregate,
            Parameter::class => $this->entity->parent->parent->aggregate,
            Property::class => $this->entity->parent->aggregate,
            Type::class => $this->entity->parent instanceof Property
                ? $this->entity->parent->parent->aggregate
                : $this->entity->parent->parent->parent->aggregate,
        };
    }

    public function getBlueprint(): Blueprint
    {
        return match ($this->type) {
            Attribute::class => $this->entity->parent instanceof Blueprint
                ? $this->entity->parent
                : $this->entity->parent->parent,
            Blueprint::class => $this->entity,
            Method::class => $this->entity->parent,
            Parameter::class => $this->entity->parent->parent,
            Property::class => $this->entity->parent,
            Type::class => $this->entity->parent instanceof Property
                ? $this->entity->parent->parent
                : $this->entity->parent->parent->parent,
        };
    }
}
