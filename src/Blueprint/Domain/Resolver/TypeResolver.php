<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Resolver;

use PBaszak\UltraMapper\Blueprint\Application\Enum\TypeDeclaration;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\ClassNotFoundException;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\PseudoTypes\False_;
use phpDocumentor\Reflection\PseudoTypes\True_;
use phpDocumentor\Reflection\Type as PhpDocumentorReflectionType;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Intersection;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;

/**
 * The resolver of the property or param type.
 */
class TypeResolver
{
    public ?TypeDeclaration $type = null;

    /** @var string[] */
    public array $types = [];
    /** @var array<string, string[]> only if isCollection is `true` */
    public array $innerTypes = [];

    public function __construct(
        private \ReflectionProperty|\ReflectionParameter $reflection,
    ) {
    }

    /** @return string[] */
    public function getTypes(): array
    {
        return $this->types;
    }

    /** @return array<string, string[]> */
    public function getInnerTypes(): array
    {
        return $this->innerTypes;
    }

    public function getTypeDeclaration(): TypeDeclaration
    {
        return $this->type ?? TypeDeclaration::UNKNOWN;
    }

    public function process(): self
    {
        $docBlockTypeRef = $this->reflection instanceof \ReflectionProperty
            ? $this->getTypeDeclarationFromVarDocBlock($this->reflection)
            : $this->getParameterTypeFromParamDocBlock($this->reflection);
        $this->processReflectionType($this->reflection->getType());
        $this->processPhpDocumentorReflectionType(
            $docBlockTypeRef,
            $this->reflection->getDeclaringClass() ?? throw new ClassNotFoundException('Class not found for '.$this->reflection->getName().' property.', 5932)
        );

        $this->setTypeFromPHPReflection($this->reflection);
        if (null === $this->type) {
            $this->setTypeFromDocBlock($docBlockTypeRef);
        }
        if (null === $this->type) {
            $this->type = TypeDeclaration::UNKNOWN;
        }

        return $this;
    }

    private function getTypeDeclarationFromVarDocBlock(\ReflectionProperty $property): ?PhpDocumentorReflectionType
    {
        $docBlock = $property->getDocComment();
        if (false === $docBlock) {
            return null;
        }

        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create($docBlock);

        /** @var Var_[] $varTags */
        $varTags = $docBlock->getTagsByName('var');
        if (empty($varTags)) {
            return null;
        }

        return $varTags[0]->getType();
    }

    private function setTypeFromPHPReflection(\ReflectionProperty|\ReflectionParameter $reflection): void
    {
        $reflection = $reflection->getType();
        $this->type = match (true) {
            $reflection instanceof \ReflectionIntersectionType => TypeDeclaration::INTERSECTION,
            $reflection instanceof \ReflectionNamedType => TypeDeclaration::NAMED,
            $reflection instanceof \ReflectionUnionType => TypeDeclaration::UNION,
            default => null,
        };

        // if null|(type1&type2) then it's nullable intersection type
        if ($reflection instanceof \ReflectionUnionType) {
            $types = $reflection->getTypes();
            if (2 === count($types)) {
                $hasNull = false;
                $hasIntersection = false;
                foreach ($types as $type) {
                    if ($type instanceof \ReflectionNamedType && 'null' === $type->getName()) {
                        $hasNull = true;
                    } elseif ($type instanceof \ReflectionIntersectionType) {
                        $hasIntersection = true;
                    }
                }

                if ($hasNull && $hasIntersection) {
                    $this->type = TypeDeclaration::INTERSECTION;
                }
            }
        }
    }

    private function setTypeFromDocBlock(?PhpDocumentorReflectionType $reflection): void
    {
        if (null === $reflection) {
            return;
        }

        if ($reflection instanceof Compound) {
            $types = $reflection->getIterator();

            // if null|type then it's nullable named type
            if (2 === count($this->types) && in_array('null', $this->types)) {
                $this->type = TypeDeclaration::NAMED;

                return;
            }

            // if null|(type1&type2) then it's nullable intersection type
            // but it's not supported by phpDocumentor to get nullable intersection type
            // if (2 === count($types)) {
            //     $hasNull = false;
            //     $hasIntersection = false;
            //     foreach ($types as $type) {
            //         if ($type instanceof Null_) {
            //             $hasNull = true;
            //         } elseif ($type instanceof Intersection) {
            //             $hasIntersection = true;
            //         }
            //     }

            //     if ($hasNull && $hasIntersection) {
            //         $this->type = TypeDeclaration::INTERSECTION;

            //         return;
            //     }
            // }

            // any other case is union type
            $this->type = TypeDeclaration::UNION;

            return;
        }

        $this->type = match (true) {
            $reflection instanceof Intersection => TypeDeclaration::INTERSECTION,
            default => TypeDeclaration::NAMED,
        };
    }

    private function getParameterTypeFromParamDocBlock(\ReflectionParameter $parameter): ?PhpDocumentorReflectionType
    {
        $constructor = $parameter->getDeclaringFunction();
        $docBlock = $constructor->getDocComment();
        if (false === $docBlock) {
            return null;
        }

        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create($docBlock);

        /** @var array<Param|InvalidTag> $paramTags */
        $paramTags = $docBlock->getTagsByName('param');
        if (empty($paramTags)) {
            return null;
        }

        $paramTag = array_filter(
            $paramTags,
            fn (Param|InvalidTag $paramTag) => $paramTag instanceof Param && $paramTag->getVariableName() === $parameter->getName()
        );

        if (empty($paramTag)) {
            return null;
        }

        return $paramTag[0]->getType();
    }

    private function processReflectionType(?\ReflectionType $reflection): void
    {
        if (null === $reflection) {
            return;
        }

        // multitypes
        if ($reflection instanceof \ReflectionUnionType || $reflection instanceof \ReflectionIntersectionType) {
            $types = $reflection->getTypes();
            foreach ($types as $type) {
                $this->processReflectionType($type);
            }

            return;
        }

        if ($reflection instanceof \ReflectionNamedType) {
            if ($reflection->allowsNull()) {
                $this->addType('null');
            }
            $type = $reflection->getName();

            // class type
            if ($this->isClassExists('\\'.ltrim($type, '\\'))) {
                $type = '\\'.ltrim($type, '\\');
            }

            // collections
            if ('array' === $type || (
                $this->isClassExists($type)
                && is_subclass_of($type, \ArrayAccess::class)
            )) {
                $this->addInnerType('mixed', 'string|int');
            }

            // single type
            $this->addType($type);
        }
    }

    private function processPhpDocumentorReflectionType(?PhpDocumentorReflectionType $reflection, \ReflectionClass $classReflection): void
    {
        if (null === $reflection) {
            return;
        }

        // multitypes
        if ($reflection instanceof Compound || $reflection instanceof Intersection) {
            $types = $reflection->getIterator();
            foreach ($types as $type) {
                $this->processPhpDocumentorReflectionType($type, $classReflection);
            }

            return;
        }

        // collections
        if ($reflection instanceof Array_ || $reflection instanceof Collection) {
            if ($reflection instanceof Array_) {
                $this->addType('array');
            }

            if ($reflection instanceof Collection) {
                $class = $reflection->getFqsen()?->__toString();
                if (null !== $class) {
                    $class = $this->getCorrectClassName($class, $classReflection);
                }

                if (null === $class) {
                    throw new ClassNotFoundException('Class not found for '.(string) $reflection.'.', 5933);
                }

                $this->addType($class);
            }

            $this->processPhpDocumentorReflectionInnerType($reflection, $classReflection);

            return;
        }

        // class type
        if ($reflection instanceof Object_) {
            $class = $reflection->__toString();
            $class = $this->getCorrectClassName($class, $classReflection);

            $this->addType($class ?? 'object');

            if ($class && is_subclass_of($class, \ArrayAccess::class)) {
                $this->addInnerType('mixed', 'string|int');
            }

            return;
        }

        if (
            $reflection instanceof PseudoType
            && !($reflection instanceof True_ || $reflection instanceof False_)
        ) {
            $reflection = $reflection->underlyingType();
        }

        if ($reflection instanceof Mixed_) {
            $this->addType('null');
        }

        // single type
        $this->addType((string) $reflection);
    }

    private function processPhpDocumentorReflectionInnerType(Array_|Collection $reflection, \ReflectionClass $classReflection): void
    {
        $keyType = $reflection->getKeyType();
        $itemType = $reflection->getValueType();

        // if key is defined then ReflectionType doesn't matter and library have to correct it
        if ('string' === (string) $keyType) {
            unset($this->innerTypes['int']);
        }
        if ('int' === (string) $keyType) {
            unset($this->innerTypes['string']);
        }

        // multitypes
        if ($itemType instanceof Compound || $itemType instanceof Intersection) {
            $types = $itemType->getIterator();
            foreach ($types as $type) {
                $this->addCorrectInnerType((string) $keyType, $type, $classReflection);
            }

            return;
        }

        $this->addCorrectInnerType((string) $keyType, $itemType, $classReflection);
    }

    private function addCorrectInnerType(string $keyType, PhpDocumentorReflectionType $itemType, \ReflectionClass $classReflection): void
    {
        if ($itemType instanceof Array_) {
            $this->addInnerType('array', $keyType);

            return;
        }

        if ($itemType instanceof Collection || $itemType instanceof Object_) {
            $class = $itemType->getFqsen()?->__toString();
            if (null !== $class) {
                $class = $this->getCorrectClassName($class, $classReflection);
            }

            if (null === $class) {
                throw new ClassNotFoundException('Class not found for '.(string) $itemType.'.', 5934);
            }

            $this->addInnerType($class, $keyType);

            return;
        }

        // class not exists! It's simple type
        $this->addInnerType((string) $itemType, $keyType);
    }

    /**
     * @return class-string|null
     */
    private function getCorrectClassName(string $possibleClass, \ReflectionClass $originClassReflection): ?string
    {
        $class = '\\'.ltrim($possibleClass, '\\');
        if ($this->isClassExists($class)) {
            return $class;
        } elseif ($this->isClassExists($classWithNamespace = '\\'.ltrim($originClassReflection->getNamespaceName().$class, '\\'))) {
            return $classWithNamespace;
        } else {
            $imports = array_filter(array_map(
                fn (string $line) => str_starts_with($line, 'use') ?
                    (false !== strpos($line, ltrim($class, '\\')) ?
                        trim(sscanf($line, 'use %s;')[0], '\\;') :
                        null
                    ) :
                    null,
                file($originClassReflection->getFileName() ?: '') ?: []
            ));

            /** @var class-string[] $imports */
            foreach ($imports as $import) {
                $class = '\\'.ltrim($import, '\\');
                if ($this->isClassExists($class)) {
                    return $class;
                }
            }
        }

        return null;
    }

    /** @var array<string, bool> */
    private array $isClassExistsCache = [];

    private function isClassExists(string $class): bool
    {
        return $this->isClassExistsCache[$class] ??= class_exists($class, true) ?: interface_exists($class, true) ?: enum_exists($class, true);
    }

    private function addType(string $type): void
    {
        if ('?' === $type[0]) {
            $type = substr($type, 1);
            $this->addType('null');
        }

        if (!in_array($type, $this->types)) {
            $this->types[] = $type;
        }
    }

    private function addInnerType(string $type, string $keyType): void
    {
        $key = explode('|', $keyType);

        foreach ($key as $keyType) {
            if (!isset($this->innerTypes[$keyType])) {
                $this->innerTypes[$keyType] = [];
            }

            if (!in_array($type, $this->innerTypes[$keyType])) {
                $this->innerTypes[$keyType][] = $type;
            }

            // removing `mixed` type from inner types
            $isNull = in_array('null', $this->innerTypes[$keyType]) ? 1 : 0;
            $isMixed = in_array('mixed', $this->innerTypes[$keyType]) ? 1 : 0;

            if ($isMixed && count($this->innerTypes[$keyType]) > 1 + $isNull) {
                unset($this->innerTypes[$keyType][array_search('mixed', $this->innerTypes[$keyType])]);
                $this->innerTypes[$keyType] = array_values($this->innerTypes[$keyType]);
            }
        }
    }
}
