<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Factories;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\TypeReflection;

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
