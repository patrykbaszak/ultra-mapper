<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Resolver;

use PBaszak\UltraMapper\Blueprint\Domain\Exception\ClassNotFoundException;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type as PhpDocumentorReflectionType;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Intersection;
use phpDocumentor\Reflection\Types\Object_;

/**
 * The resolver of the property or param type.
 */
class TypeResolver
{
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

    public function process(): self
    {
        $this->processReflectionType($this->reflection->getType());
        $this->processPhpDocumentorReflectionType(
            $this->reflection instanceof \ReflectionProperty
                ? $this->getTypeDeclarationFromVarDocBlock($this->reflection)
                : $this->getParameterTypeFromParamDocBlock($this->reflection),
            $this->reflection->getDeclaringClass() ?? throw new ClassNotFoundException('Class not found for '.$this->reflection->getName().' property.', 5932)
        );

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

            return;
        }

        // single type
        $this->addType((string) $reflection);
    }

    private function processPhpDocumentorReflectionInnerType(Array_|Collection $reflection, \ReflectionClass $classReflection): void
    {
        $keyType = $reflection->getKeyType();
        $itemType = $reflection->getValueType();

        if ($itemType instanceof Array_) {
            $this->addInnerType('array', (string) $keyType);

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

            $this->addInnerType($class, (string) $keyType);

            return;
        }

        // class not exists! It's simple type
        $this->addInnerType((string) $itemType, (string) $keyType);
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
        if ('?' === $type[0]) {
            $type = substr($type, 1);
            $this->addInnerType('null', $keyType);
        }

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

        // removing `string|int` from key types if any other key type exists
        if (count($this->innerTypes) > 1) {
            unset($this->innerTypes['string|int']);
        }
    }
}
