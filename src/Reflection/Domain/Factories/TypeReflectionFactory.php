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
            $this->getDocBlockReflectionTypeFromParamTag($reflectionParameter),
            $reflectionParameter->getDeclaringClass()
        );
    }

    public function createForProperty(\ReflectionProperty $reflectionProperty): TypeReflection
    {
        return $this->create(
            $reflectionProperty->getType(),
            $this->getDocBlockReflectionTypeFromVarTag($reflectionProperty),
            $reflectionProperty->getDeclaringClass()
        );
    }

    public function createForMethod(\ReflectionMethod $reflectionMethod): TypeReflection
    {
        return $this->create(
            $reflectionMethod->getReturnType(),
            $this->getDocBlockReflectionTypeFromReturnTag($reflectionMethod),
            $reflectionMethod->getDeclaringClass()
        );
    }

    public function create(?\ReflectionType $ref, ?PhpDocumentorReflectionType $docCommentRef, ?\ReflectionClass $declarationClass): TypeReflection
    {
        $reflectionType = $this->createTypeReflectionBasedOnReflectionType($ref);
        if ($declarationClass) {
            $phpDocumentatorType = $this->createTypeReflectionBasedOnPhpDocumentatorReflectionType($docCommentRef, $declarationClass);
        }

        if (!isset($phpDocumentatorType) || $reflectionType === $phpDocumentatorType) {
            return $reflectionType;
        }

        if ($reflectionType instanceof NamedTypeReflection && $phpDocumentatorType instanceof NamedTypeReflection) {
            return $this->mergeNamedTypeReflections($reflectionType, $phpDocumentatorType);
        }

        if ($reflectionType instanceof IntersectionTypeReflection || $phpDocumentatorType instanceof IntersectionTypeReflection) {
            return $this->mergeIntersectionTypeReflections($reflectionType, $phpDocumentatorType);
        }
        // todo

        return $reflectionType;
    }

    private function mergeNamedTypeReflections(?NamedTypeReflection $reflectionType, ?NamedTypeReflection $phpDocumentatorType): NamedTypeReflection
    {
        // if there is no reflection type and no phpDocumentator type
        if (null === $reflectionType && null === $phpDocumentatorType) {
            return NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN);

        // if there is no reflection type or no phpDocumentator type
        } elseif (null === $reflectionType || null === $phpDocumentatorType) {
            return $reflectionType ?? $phpDocumentatorType;

        // if the reflection type and phpDocumentator type are the same
        } elseif ($reflectionType->name() === $phpDocumentatorType->name()) {
            return $reflectionType;

        // if the php documentator reflection type is mixed then it does not matter what is the reflection type
        } elseif ('mixed' === $phpDocumentatorType->name()) {
            return $reflectionType;

        // if the reflection type is mixed then it does not matter what is the php documentator reflection type
        } elseif ('mixed' === $reflectionType->name()) {
            return $phpDocumentatorType;

        // if the php documentator reflection type is advanced object and the reflection type is not at least object or mixed
        } elseif ($phpDocumentatorType->flags() > 1 && 1 == $reflectionType->flags() && !in_array($reflectionType->name(), ['mixed', 'object'])) {
            throw new \LogicException('\ReflectionType and phpDocumentator reflection type are not compatible.', 16);
        }

        /* Class should be more important thant abstract class */
        [$updatedReflectionType, $updatedPhpDocumentatorType] = array_map(
            fn (NamedTypeReflection $type) => $type->isClass() && !$type->isAbstractClass() ? NamedTypeReflection::recreate($type->name(), $type->flags() | 32) : $type,
            [$reflectionType, $phpDocumentatorType]
        );

        return $updatedReflectionType->flags() >= $updatedPhpDocumentatorType->flags()
            ? $reflectionType
            : $phpDocumentatorType;
    }

    private function mergeIntersectionTypeReflections(?TypeReflection $reflectionType, ?TypeReflection $phpDocumentatorType): IntersectionTypeReflection
    {
        // if there is no reflection type and no phpDocumentator type
        if (null === $reflectionType && null === $phpDocumentatorType) {
            throw new \LogicException('One of the reflection types has to be set.', 17);
        // accepts only IntersectionTypeReflection
        } elseif (!$reflectionType instanceof IntersectionTypeReflection && !$phpDocumentatorType instanceof IntersectionTypeReflection) {
            throw new \LogicException('The reflection types have to be IntersectionTypeReflection.', 18);
        // if there is no reflection type or no phpDocumentator type
        } elseif (null === $reflectionType || null === $phpDocumentatorType) {
            return $reflectionType ?? $phpDocumentatorType;

        // in there are two reflection the phpDocumentator is more important
        } elseif ($phpDocumentatorType instanceof IntersectionTypeReflection) {
            return $phpDocumentatorType;
        }

        // note:
        // The resposibility of using doc comment with type hint is on the programmer of the application - not author of the package. Thank you for understanding. ~ Patryk Baszak

        return $reflectionType;
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
            $flags = 0;
            if (method_exists($ref, 'getFqsen') && null !== $ref->getFqsen()) {
                $class = $this->resolveReflectionClass($ref->getFqsen()->__toString(), $declarationClass);
                $flags = $this->setFlagsForClass($class);
            }

            return CollectionTypeReflection::create(
                match (get_class($ref)) {
                    Array_::class => NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    Iterable_::class => NamedTypeReflection::create('iterable', NamedTypeReflection::IS_BUILT_IN),
                    Collection::class => NamedTypeReflection::create(@$class->getName() ?? $ref->getFqsen()?->__toString() ?? 'object', $flags),
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
            $class = $this->resolveReflectionClass($ref->getFqsen()->__toString(), $declarationClass);

            $isCollection = $class->implementsInterface(\Traversable::class) || $class->implementsInterface(\ArrayAccess::class);
            $flags = $this->setFlagsForClass($class);

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

        // if the reflection is not recognized intersection or compound
        if (str_contains($ref->__toString(), '|') || str_contains($ref->__toString(), '&')) {
            $docBlock = '/** @var '.$ref->__toString().' */';
            $factory = DocBlockFactory::createInstance();
            $context = (new ContextFactory())->createFromReflector($declarationClass);
            $docBlock = $factory->create($docBlock, $context);

            /** @var Var_[] $tags */
            $tags = $docBlock->getTagsByName('var');

            $type = $tags[0]->getType();

            return $this->createTypeReflectionBasedOnPhpDocumentatorReflectionType($type, $declarationClass);
        }

        // if the reflection is built-in
        $flags |= NamedTypeReflection::IS_BUILT_IN;

        return NamedTypeReflection::create($ref->__toString(), $flags);
    }

    private function resolveReflectionClass(string $fqsen, \ReflectionClass $declarationClass): \ReflectionClass
    {
        $returnIfExists = function (string $fqsen): ?\ReflectionClass {
            if (class_exists($fqsen, false)) {
                return new \ReflectionClass($fqsen);
            }

            if (interface_exists($fqsen, false)) {
                return new \ReflectionClass($fqsen);
            }

            if (enum_exists($fqsen, false)) {
                return new \ReflectionEnum($fqsen);
            }

            return null;
        };

        $possibleClasses = array_filter([
            $fqsen,
            '\\'.ltrim($fqsen, '\\'),
            '\\'.ltrim($declarationClass->getNamespaceName(), '\\').'\\'.$fqsen,
            '\\'.ltrim($declarationClass->getNamespaceName(), '\\').'\\'.ltrim($fqsen, '\\'),
            $this->findMatchingImport($declarationClass->getFileName(), $fqsen),
            $this->findMatchingImport($declarationClass->getFileName(), $fqsen) ? '\\'.ltrim($this->findMatchingImport($declarationClass->getFileName(), $fqsen), '\\') : null,
        ]);

        foreach ($possibleClasses as $possibleClass) {
            if (null !== $possibleClass && null !== $returnIfExists($possibleClass)) {
                return $returnIfExists($possibleClass);
            }
        }

        throw new \LogicException("Class, interface or enum $fqsen (should be full name with namespace) not found. ".'Maybe the package require an update. On this stage the Object_ has to be a class, interface or enum.');
    }

    private function findMatchingImport(false|string $fileName, string $fqsen): ?string
    {
        if (false === $fileName) {
            throw new \LogicException('The file name is not valid.');
        }

        /** @var string[] $fileLines */
        $fileLines = file($fileName);

        $useStatements = array_filter($fileLines, function ($line) {
            return 0 === strpos(trim($line), 'use ');
        });

        $useStatements = array_map(function ($line) {
            return trim(str_replace(['use ', ';'], '', $line));
        }, $useStatements);

        $matchingUses = array_filter($useStatements, function ($useStatement) use ($fqsen) {
            return false !== strpos($useStatement, ltrim($fqsen, '\\'));
        });

        $matchingUses = array_map(function ($useStatement) {
            return explode(' as ', $useStatement);
        }, $matchingUses);

        return $matchingUses ? array_values($matchingUses)[0][0] : null;
    }

    private function setFlagsForClass(\ReflectionClass $ref): int
    {
        $flags = 0;

        if ($ref->isInterface()) {
            $flags |= NamedTypeReflection::IS_INTERFACE;
        } elseif ($ref->isAbstract()) {
            $flags |= NamedTypeReflection::IS_ABSTRACT | NamedTypeReflection::IS_CLASS;
        } elseif ($ref->isEnum()) {
            $flags |= NamedTypeReflection::IS_ENUM;
        } else {
            $flags |= NamedTypeReflection::IS_CLASS;
        }

        return $flags;
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
