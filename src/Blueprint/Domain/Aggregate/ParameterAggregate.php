<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Aggregate;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Method;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Parameter;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\TypeNotDeclaredException;
use PBaszak\UltraMapper\Blueprint\Domain\Normalizer\Normalizable;

class ParameterAggregate implements Normalizable
{
    public function __construct(
        public Method $root,
        /** @var array<string, Parameter> $parameters */
        public array $parameters,
    ) {
    }

    public static function create(Method $root): self
    {
        $ref = $root->getReflection();

        $parameters = [];
        foreach ($ref->getParameters() as $parameter) {
            try {
                $parameters[$parameter->getName()] = Parameter::create($parameter, $root);
            } catch (TypeNotDeclaredException $e) {
                if ($root->isConstructor) {
                    throw $e;
                }
            }
        }

        return new self($root, $parameters);
    }

    public function normalize(): array
    {
        return array_map(fn (Normalizable&Parameter $parameter) => $parameter->normalize(), $this->parameters);
    }
}
