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
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\PseudoTypes\False_;
use phpDocumentor\Reflection\PseudoTypes\True_;
use phpDocumentor\Reflection\Type as PhpDocumentorReflectionType;
use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Intersection;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Parent_;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\This;

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
        // if there is no reflection type
        if (null === $ref) {
            return NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN);
        }

        // if the reflection type is a union
        if ($ref instanceof \ReflectionUnionType) {
            $types = array_map(
                fn (\ReflectionType $type) => $this->createTypeReflectionBasedOnReflectionType($type),
                $ref->getTypes()
            );

            return UnionTypeReflection::create($types);
        }

        // if the reflection type is an intersection
        if ($ref instanceof \ReflectionIntersectionType) {
            $types = array_map(
                fn (\ReflectionType $type) => $this->createTypeReflectionBasedOnReflectionType($type),
                $ref->getTypes()
            );

            return IntersectionTypeReflection::create($types);
        }

        // if the reflection is a collection or named
        if (!$ref instanceof \ReflectionNamedType) {
            throw new \LogicException('The package require an update. On this stage the ReflectionType has to be an ReflectionNamedType.');
        }

        /** @var \ReflectionNamedType $ref */
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

        // if the reflection type allows null
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

    private function createTypeReflectionBasedOnPhpDocumentatorReflectionType(?PhpDocumentorReflectionType $ref, \ReflectionClass $declarationClass): TypeReflection
    {
        // if there is no reflection type
        if (null === $ref) {
            return NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN);
        }

        // if the reflection type is a pseudo-type
        if ($ref instanceof PseudoType && !$ref instanceof True_ && !$ref instanceof False_) {
            $ref = $ref->underlyingType();
        }

        // if the reflection type is a union
        if ($ref instanceof Compound) {
            $types = array_map(
                fn (PhpDocumentorReflectionType $type) => $this->createTypeReflectionBasedOnPhpDocumentatorReflectionType($type, $declarationClass),
                $ref->getIterator()->getArrayCopy()
            );

            return UnionTypeReflection::create($types);
        }

        // if the reflection type is an intersection
        if ($ref instanceof Intersection) {
            $types = array_map(
                fn (PhpDocumentorReflectionType $type) => $this->createTypeReflectionBasedOnPhpDocumentatorReflectionType($type, $declarationClass),
                $ref->getIterator()->getArrayCopy()
            );

            return IntersectionTypeReflection::create($types);
        }

        // if the reflection is a collection
        if ($ref instanceof AbstractList || in_array(get_class($ref), [Array_::class, Iterable_::class])) {
            return CollectionTypeReflection::create(
                match (get_class($ref)) {
                    Array_::class => NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    Iterable_::class => NamedTypeReflection::create('iterable', NamedTypeReflection::IS_BUILT_IN),
                    Collection::class => NamedTypeReflection::create($ref->getFqsen()?->__toString() ?? 'object', NamedTypeReflection::IS_CLASS),
                },
                $this->createTypeReflectionBasedOnPhpDocumentatorReflectionType($ref->getKeyType(), $declarationClass),
                $this->createTypeReflectionBasedOnPhpDocumentatorReflectionType($ref->getValueType(), $declarationClass)
            );
        }

        // it the reflection is object pseudo-type
        $ref = match (get_class($ref)) {
            Self_::class, Static_::class, This::class => new Object_(new Fqsen('\\'.ltrim($declarationClass->getName(), '\\'))),
            Parent_::class => $declarationClass->getParentClass()
                ? new Object_(new Fqsen('\\'.ltrim($declarationClass->getParentClass()->getName(), '\\')))
                : throw new \LogicException('The reflection is Parent but the class does not have a parent.'),
            default => $ref,
        };

        $flags = 0;
        // if the reflection is class, interface or enum
        if ($ref instanceof Object_ && null !== $ref->getFqsen()) {
            $class = (new \ReflectionClass($ref->getFqsen()->__toString()));
            $isClass = true;
            $isCollection = $class->implementsInterface(\Traversable::class) || $class->implementsInterface(\ArrayAccess::class);
            if ($class->isAbstract()) {
                $flags |= NamedTypeReflection::IS_ABSTRACT;
            }
            if ($class->isInterface()) {
                $flags |= NamedTypeReflection::IS_INTERFACE;
                $isClass = false;
            }
            if ($class->isEnum()) {
                $flags |= NamedTypeReflection::IS_ENUM;
                $isClass = false;
            }
            if ($isClass) {
                $flags |= NamedTypeReflection::IS_CLASS;
            }

            return $isCollection
                ? CollectionTypeReflection::create(
                    NamedTypeReflection::create($class->getName(), $flags),
                    \SplObjectStorage::class === $class->getName() || $class->isSubclassOf(\SplObjectStorage::class)
                        ? NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN)
                        : UnionTypeReflection::create([
                            NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                            NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
                )
                : NamedTypeReflection::create($class->getName(), $flags);
        }

        // if the reflection allows null
        if ($ref instanceof Nullable) {
            return UnionTypeReflection::create([
                NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                $this->createTypeReflectionBasedOnPhpDocumentatorReflectionType($ref->getActualType(), $declarationClass),
            ]);
        }

        // if the reflection is built-in
        $flags |= NamedTypeReflection::IS_BUILT_IN;

        return NamedTypeReflection::create($ref->__toString(), $flags);
    }

    private function getDocBlockReflectionTypeFromVarTag(\ReflectionProperty $ref): ?PhpDocumentorReflectionType
    {
        $docBlock = $ref->getDocComment();
        if (false === $docBlock) {
            return null;
        }

        $factory = DocBlockFactory::createInstance();
        $context = (new ContextFactory())->createFromReflector($ref);
        $docBlock = $factory->create($docBlock, $context);

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
        $context = (new ContextFactory())->createFromReflector($ref);
        $docBlock = $factory->create($docBlock, $context);

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
        $context = (new ContextFactory())->createFromReflector($ref);
        $docBlock = $factory->create($docBlock, $context);

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
