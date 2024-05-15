<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Model;

use PBaszak\UltraMapper\Blueprint\Application\Enum\TypeDeclaration;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ClassBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\ParameterBlueprint;
use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Resolver\TypeResolver;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

class Type implements Normalizable
{
    public PropertyBlueprint|ParameterBlueprint $parent;

    public TypeDeclaration $type;
    /** @var string[] */
    public array $types = [];
    /** @var array<string, string[]> */
    public array $innerTypes = [];
    /** @var array<class-string, string> */
    public array $blueprints = [];

    public static function create(PropertyBlueprint|ParameterBlueprint $parent): self
    {
        $instance = new self();
        $instance->parent = $parent;
        $typeResolver = new TypeResolver($parent->getReflection());
        $typeResolver->process();
        $instance->type = $typeResolver->type ?? TypeDeclaration::UNKNOWN;
        $instance->types = $typeResolver->types;
        $instance->innerTypes = $typeResolver->innerTypes;

        if (empty($instance->types) && empty($instance->innerTypes)) {
            $instance->types = ['mixed'];
        }

        if ($parent instanceof PropertyBlueprint && 0 < count($classTypes = $instance->getAllClassTypes())) {
            if (null === $aggregate = Blueprint::getBlueprint($instance->parent)) {
                $aggregate = Blueprint::create(Blueprint::getClassBlueprint($instance->parent));
            }

            foreach ($classTypes as $class) {
                $blueprint = ClassBlueprint::create($class, $parent, $aggregate);
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

    public function getReflection(): ?\ReflectionType
    {
        return $this->parent->getReflection()->getType();
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
