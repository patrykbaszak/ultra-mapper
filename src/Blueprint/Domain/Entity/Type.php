<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Application\Enum\TypeDeclaration;
use PBaszak\UltraMapper\Blueprint\Domain\Accessor\Accessor;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\BlueprintAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\TypeNotDeclaredException;
use PBaszak\UltraMapper\Blueprint\Domain\Normalizer\Normalizable;
use PBaszak\UltraMapper\Blueprint\Domain\Resolver\TypeResolver;

/**
 * The representation of a class or a property attribute.
 */
class Type implements Normalizable
{
    public Property|Parameter $parent;

    public TypeDeclaration $type;
    /** @var string[] */
    public array $types = [];
    /** @var array<string, string[]> */
    public array $innerTypes = [];
    /** @var array<class-string, string> */
    public array $blueprints = [];

    public static function create(Property|Parameter $parent): self
    {
        $instance = new self();
        $instance->parent = $parent;
        $typeResolver = new TypeResolver($parent->getReflection());
        $typeResolver->process();
        $instance->types = $typeResolver->types;
        $instance->innerTypes = $typeResolver->innerTypes;

        if (empty($instance->types) && empty($instance->innerTypes)) {
            $exception = $parent instanceof Property
                ? new TypeNotDeclaredException('Type not declared for '.$parent->originName.' property. '.$parent->parent->name.' class.', 5941)
                : new TypeNotDeclaredException('Type not declared for '.$parent->name.' parameter. '.$parent->parent->name.' method. '.$parent->parent->parent->name.' class.', 5942);

            throw $exception;
        }

        if ($parent->getReflection()->getType()) {
            $instance->type = match (true) {
                $parent->getReflection()->getType() instanceof \ReflectionNamedType => TypeDeclaration::NAMED,
                $parent->getReflection()->getType() instanceof \ReflectionUnionType => TypeDeclaration::UNION,
                $parent->getReflection()->getType() instanceof \ReflectionIntersectionType => TypeDeclaration::INTERSECTION,
                default => TypeDeclaration::UNKNOWN,
            };
        } else {
            $types = $instance->types;
            if (in_array('null', $types)) {
                $types = array_diff($types, ['null']);
            }
            $instance->type = match (true) {
                count($instance->types) > 1 => TypeDeclaration::UNION,
                1 === count($instance->types) => TypeDeclaration::NAMED,
                default => TypeDeclaration::UNKNOWN,
            };
        }

        if ($parent instanceof Property && 0 < count($classTypes = $instance->getAllClassTypes())) {
            if (null === $aggregate = (new Accessor($instance))->getBlueprintAggregate()) {
                $aggregate = BlueprintAggregate::create((new Accessor($instance))->getBlueprint());
            }

            foreach ($classTypes as $class) {
                $blueprint = Blueprint::create($class, $parent, $aggregate);
                $instance->blueprints[$class] = $blueprint->blueprintName;
                $aggregate->addBlueprint($blueprint);
            }
        }

        return $instance;
    }

    /** @return class-string[] */
    public function getAllClassTypes(): array
    {
        $classTypes = [];
        $function = function ($type) use (&$classTypes) {
            if (class_exists($type)) {
                $classTypes[] = $type;
            }
        };
        array_walk_recursive($this->innerTypes, $function);
        array_walk($this->types, $function);

        return $classTypes;
    }

    public function normalize(): array
    {
        return [
            'type' => $this->type->value,
            'types' => $this->types,
            'innerTypes' => $this->innerTypes,
            'blueprints' => $this->blueprints,
        ];
    }
}
