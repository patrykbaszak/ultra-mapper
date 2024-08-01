<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Factories;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\NamedTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\TypeReflection;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type as PhpDocumentorReflectionType;

class TypeReflectionFactory
{
    public function createForParameter(\ReflectionParameter $reflectionParameter): TypeReflection
    {
        return $this->create(
            $reflectionParameter->getType(),
            $this->getDocBlockReflectionTypeFromParamTag($reflectionParameter)
        );
    }

    public function createForProperty(\ReflectionProperty $reflectionProperty): TypeReflection
    {
        return $this->create(
            $reflectionProperty->getType(),
            $this->getDocBlockReflectionTypeFromVarTag($reflectionProperty)
        );
    }

    public function createForMethod(\ReflectionMethod $reflectionMethod): TypeReflection
    {
        return $this->create(
            $reflectionMethod->getReturnType(),
            $this->getDocBlockReflectionTypeFromReturnTag($reflectionMethod)
        );
    }

    public function create(?\ReflectionType $ref, ?PhpDocumentorReflectionType $docCommentRef): TypeReflection
    {
        return NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILD_IN);
    }

    private function getDocBlockReflectionTypeFromVarTag(\ReflectionProperty $ref): ?PhpDocumentorReflectionType
    {
        $docBlock = $ref->getDocComment();
        if (false === $docBlock) {
            return null;
        }

        $factory = DocBlockFactory::createInstance();
        $docBlock = $factory->create($docBlock);

        /** @var Var_[] $tags */
        $tags = $docBlock->getTagsByName('var');

        if (0 === count($tags)) {
            return null;
        }

        return $tags[0]->getType();
    }

    private function getDocBlockReflectionTypeFromReturnTag(\ReflectionMethod $ref): ?PhpDocumentorReflectionType
    {
        $docBlock = $ref->getDocComment();
        if (false === $docBlock) {
            return null;
        }

        $factory = DocBlockFactory::createInstance();
        $docBlock = $factory->create($docBlock);

        /** @var Return_[] $tags */
        $tags = $docBlock->getTagsByName('return');

        if (0 === count($tags)) {
            return null;
        }

        return $tags[0]->getType();
    }

    private function getDocBlockReflectionTypeFromParamTag(\ReflectionParameter $ref): ?PhpDocumentorReflectionType
    {
        $docBlock = $ref->getDeclaringFunction()->getDocComment();
        if (false === $docBlock) {
            return null;
        }

        $factory = DocBlockFactory::createInstance();
        $docBlock = $factory->create($docBlock);

        /** @var Param[] $tags */
        $tags = $docBlock->getTagsByName('param');

        if (0 === count($tags)) {
            return null;
        }

        foreach ($tags as $tag) {
            if ($tag->getVariableName() === $ref->getName()) {
                return $tag->getType();
            }
        }

        return null;
    }
}
