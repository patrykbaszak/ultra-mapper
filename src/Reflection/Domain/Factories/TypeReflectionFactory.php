<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Factories;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\TypeReflection;

/**
 * Type is returned in the array of TypeReflection where the key is the
 * deepness of the type in the reflection hierarchy.
 *
 * Like: array<int, string>
 * [
 *    0 => array,
 *    1 => int
 * ]
 */
class TypeReflectionFactory
{
    public function createForParameter(\ReflectionParameter $reflectionParameter): TypeReflection
    {
        // $typeReflection = TypeReflection::create($reflectionParameter->getType());
        // $typeReflection->raise(new ReflectionCreated($typeReflection->id()));

        // return $typeReflection;
        throw new \Exception('Not implemented');
    }

    public function createForProperty(\ReflectionProperty $reflectionProperty): TypeReflection
    {
        // $typeReflection = TypeReflection::create($reflectionProperty->getType());
        // $typeReflection->raise(new ReflectionCreated($typeReflection->id()));

        // return $typeReflection;
        throw new \Exception('Not implemented');
    }

    public function createForMethod(\ReflectionMethod $reflectionMethod): TypeReflection
    {
        // $typeReflection = TypeReflection::create($reflectionMethod->getReturnType());
        // $typeReflection->raise(new ReflectionCreated($typeReflection->id()));

        // return $typeReflection;
        throw new \Exception('Not implemented');
    }
}
