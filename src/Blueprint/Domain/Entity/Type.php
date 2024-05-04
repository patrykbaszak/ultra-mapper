<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Application\Enum\PropertyType;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\TypeNotDeclaredException;
use PBaszak\UltraMapper\Blueprint\Domain\Resolver\TypeResolver;

/**
 * The representation of a class or a property attribute.
 */
class Type
{
    public Property $parent;

    public PropertyType $type;
    /** @var string[] */
    public array $types = [];
    /** @var array<string, string[]> */
    public array $innerTypes = [];

    public static function create(Property $parent): self
    {
        $instance = new self();
        $instance->parent = $parent;
        $typeResolver = new TypeResolver($parent->getReflection());
        $typeResolver->process();
        $instance->types = $typeResolver->types;
        $instance->innerTypes = $typeResolver->innerTypes;

        if (empty($instance->types) && empty($instance->innerTypes)) {
            throw new TypeNotDeclaredException('Type not declared for '.$parent->originName.' property. '.$parent->parent->name.' class.', 5941);
        }

        if ($parent->getReflection()->getType()) {
            $instance->type = match (true) {
                $parent->getReflection()->getType() instanceof \ReflectionNamedType => PropertyType::NAMED,
                $parent->getReflection()->getType() instanceof \ReflectionUnionType => PropertyType::UNION,
                $parent->getReflection()->getType() instanceof \ReflectionIntersectionType => PropertyType::INTERSECTION,
                default => PropertyType::UNKNOWN,
            };
        } else {
            $types = $instance->types;
            if (in_array('null', $types)) {
                $types = array_diff($types, ['null']);
            }
            $instance->type = match (true) {
                count($instance->types) > 1 => PropertyType::UNION,
                1 === count($instance->types) => PropertyType::NAMED,
                default => PropertyType::UNKNOWN,
            };
        }

        return $instance;
    }
}
