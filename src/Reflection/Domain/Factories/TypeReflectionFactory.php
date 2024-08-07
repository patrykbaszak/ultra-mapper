<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Factories;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\CollectionTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\IntersectionTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\NamedTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\TypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\UnionTypeReflection;
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
        // collection
        // compound
        // intersection
        // named

        return $this->createTypeReflectionBasedOnReflectionType($ref);
    }

    private function createTypeReflectionBasedOnReflectionType(?\ReflectionType $ref): TypeReflection
    {
        if ($ref instanceof \ReflectionNamedType) {
            $name = $ref->getName();
            $flags = 0;
            $isCollection = in_array($name, ['array', 'iterable']);

            if ($ref->isBuiltin()) {
                $flags |= NamedTypeReflection::IS_BUILT_IN;
            }

            if (class_exists($name, false) && !enum_exists($name, false)) {
                $flags |= NamedTypeReflection::IS_CLASS;
                $class = (new \ReflectionClass($name));

                if ($class->isAbstract()) {
                    $flags |= NamedTypeReflection::IS_ABSTRACT;
                }

                if ($class->implementsInterface(\Traversable::class) || $class->implementsInterface(\ArrayAccess::class)) {
                    $isCollection = true;
                }
            }

            if (interface_exists($name, false)) {
                $flags |= NamedTypeReflection::IS_INTERFACE;
                $interface = (new \ReflectionClass($name));

                if ($interface->implementsInterface(\Traversable::class) || $interface->implementsInterface(\ArrayAccess::class)) {
                    $isCollection = true;
                }
            }

            if (enum_exists($name, false)) {
                $flags |= NamedTypeReflection::IS_ENUM;
            }

            $output = $isCollection
                ? CollectionTypeReflection::create(
                    NamedTypeReflection::create($name, $flags),
                    \SplObjectStorage::class === $name || (@$class && $class->isSubclassOf(\SplObjectStorage::class))
                        ? NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN)
                        : UnionTypeReflection::create([
                            NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                            NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
                )
                : NamedTypeReflection::create($name, $flags);

            if ($ref->allowsNull()) {
                if (!in_array($name, ['null', 'mixed'])) {
                    return UnionTypeReflection::create([
                        NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                        $output,
                    ]);
                }

                if ('null' === $name) {
                    return NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN);
                }

                if ('mixed' === $name) {
                    return NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN);
                }
            }

            return $output;
        }

        if ($ref instanceof \ReflectionUnionType) {
            $types = array_map(
                fn (\ReflectionType $type) => $this->createTypeReflectionBasedOnReflectionType($type),
                $ref->getTypes()
            );

            return UnionTypeReflection::create($types);
        }

        if ($ref instanceof \ReflectionIntersectionType) {
            $types = array_map(
                fn (\ReflectionType $type) => $this->createTypeReflectionBasedOnReflectionType($type),
                $ref->getTypes()
            );

            return IntersectionTypeReflection::create($types);
        }

        return NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN);
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
