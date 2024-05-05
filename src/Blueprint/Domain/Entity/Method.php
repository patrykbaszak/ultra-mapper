<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Entity;

use PBaszak\UltraMapper\Blueprint\Application\Enum\Visibility;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\ParameterAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Normalizer\Normalizable;

/**
 * The representation of the class method.
 */
class Method implements Normalizable
{
    public Blueprint $parent;
    public string $name;

    public Visibility $visibility;
    public bool $isConstructor;
    public bool $isStatic;
    public false|string $docBlock;

    public ParameterAggregate $parameters;

    public static function create(\ReflectionMethod $method, Blueprint $parent): self
    {
        $instance = new self();
        $instance->parent = $parent;
        $instance->name = $method->getName();
        $instance->visibility = match (true) {
            $method->isPublic() => Visibility::PUBLIC,
            $method->isProtected() => Visibility::PROTECTED,
            $method->isPrivate() => Visibility::PRIVATE
        };
        $instance->isConstructor = $method->isConstructor();
        $instance->isStatic = $method->isStatic();
        $instance->docBlock = $method->getDocComment() ?: false;
        $instance->parameters = ParameterAggregate::create($instance);

        return $instance;
    }

    public function getReflection(): \ReflectionMethod
    {
        return new \ReflectionMethod($this->parent->name, $this->name);
    }

    public function normalize(): array
    {
        return [
            'visibility' => $this->visibility->value,
            'isConstructor' => $this->isConstructor,
            'isStatic' => $this->isStatic,
            'docBlock' => $this->docBlock,
            'parameters' => $this->parameters->normalize(),
        ];
    }
}
