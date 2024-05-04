<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Aggregate;

use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Method;

class MethodAggregate
{
    public function __construct(
        public Blueprint $root,
        /** @var array<string, Method> $methods */
        public array $methods,
    ) {
    }

    public static function create(Blueprint $root): self
    {
        $ref = $root->getReflection();

        $properties = [];
        foreach ($ref->getMethods() as $method) {
            $properties[$method->getName()] = Method::create($method, $root);
        }

        return new self($root, $properties);
    }
}
