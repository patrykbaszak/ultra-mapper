<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Resolver;

use PBaszak\UltraMapper\Blueprint\Domain\Exception\ClassNotFoundException;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type as PhpDocumentorReflectionType;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Object_;

/**
 * The resolver of the property or param type.
 */
class TypeResolver
{
    /** @var string[] */
    public array $types = [];
    /** @var string[] only if isCollection is `true` */
    public array $innerTypes = [];

    public function __construct(
        private \ReflectionProperty|\ReflectionParameter $reflection,
    ) {
    }

    public function process(): void
    {
        $this->processReflectionType($this->reflection->getType());
        $this->processPhpDocumentorReflectionType(
            $this->reflection instanceof \ReflectionProperty
                ? $this->getPropertyTypeFromVarDocBlock($this->reflection)
                : $this->getParameterTypeFromParamDocBlock($this->reflection),
            $this->reflection->getDeclaringClass() ?? throw new ClassNotFoundException('Class not found for '.$this->reflection->getName().' property.', 5932)
        );
    }

    private function getPropertyTypeFromVarDocBlock(?\ReflectionProperty $property): ?PhpDocumentorReflectionType
    {
        if (!$property) {
            return null;
        }

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

    private function getParameterTypeFromParamDocBlock(?\ReflectionParameter $parameter): ?PhpDocumentorReflectionType
    {
        if (!$parameter) {
            return null;
        }

        $constructor = $parameter->getDeclaringFunction();
        $docBlock = $constructor->getDocComment();
        if (false === $docBlock) {
            return null;
        }

        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create($docBlock);

        /** @var Param[] $paramTags */
        $paramTags = $docBlock->getTagsByName('param');
        if (empty($paramTags)) {
            return null;
        }

        $paramTag = array_filter(
            $paramTags,
            fn (Param $paramTag) => $paramTag->getVariableName() === $parameter->getName()
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

        if ($reflection instanceof \ReflectionNamedType) {
            if ($reflection->allowsNull()) {
                $this->addType('null');
            }
            $this->addType($reflection->getName());
        }

        if ($reflection instanceof \ReflectionUnionType || $reflection instanceof \ReflectionIntersectionType) {
            $types = $reflection->getTypes();
            foreach ($types as $type) {
                $this->processReflectionType($type);
            }
        }
    }

    private function processPhpDocumentorReflectionType(?PhpDocumentorReflectionType $reflection, \ReflectionClass $classReflection): void
    {
        if (null === $reflection) {
            return;
        }

        if ($reflection instanceof Compound) {
            $types = $reflection->getIterator();
            foreach ($types as $type) {
                $this->processPhpDocumentorReflectionType($type, $classReflection);
            }

            return;
        }

        if ($reflection instanceof Array_ || $reflection instanceof Collection) {
            $itemType = $reflection->getValueType();

            $itemClass = $itemType->__toString();
            if (class_exists($itemClass, false)) {
                $this->innerTypes[] = $itemClass;
                $this->addType((string) $reflection->getKeyType());

                return;
            }

            if (class_exists($class = $classReflection->getNamespaceName().'\\'.ltrim($itemClass, '\\'), false)) {
                $this->innerTypes[] = $class;
                $this->addType((string) $reflection->getKeyType());

                return;
            }

            /** @var class-string[] $imports */
            $imports = array_filter(array_map(
                fn (string $line) => str_starts_with($line, 'use') ?
                    (false !== strpos($line, ltrim($itemClass, '\\')) ?
                        sscanf($line, 'use %s;') :
                        null
                    ) :
                    null,
                file($classReflection->getFileName() ?: '') ?: []
            ));

            foreach ($imports as $import) {
                if (class_exists($import, false)) {
                    $this->innerTypes[] = $import;
                    $this->addType((string) $reflection->getKeyType());

                    return;
                }
            }

            // class not exists! It's simple type
            $this->innerTypes[] = (string) $itemType;
            $this->addType((string) $reflection->getKeyType());

            return;
        }

        if ($reflection instanceof Object_) {
            $class = $reflection->__toString();
            if (class_exists($class, false)) {
                $this->types[] = $class;
                $this->addType((string) $class);

                return;
            }

            if (class_exists($class = $classReflection->getNamespaceName().'\\'.ltrim($class, '\\'), false)) {
                $this->types[] = $class;
                $this->addType($class);

                return;
            }

            $imports = array_filter(array_map(
                fn (string $line) => str_starts_with($line, 'use') ?
                    (false !== strpos($line, ltrim($class, '\\')) ?
                        sscanf($line, 'use %s;') :
                        null
                    ) :
                    null,
                file($classReflection->getFileName() ?: '') ?: []
            ));

            /** @var class-string[] $imports */
            foreach ($imports as $import) {
                if (class_exists($import, false)) {
                    $this->types[] = $import;
                    $this->addType($import);

                    return;
                }
            }
        }

        $this->addType((string) $reflection);
    }

    private function addType(string $type): void
    {
        if ('?' === $type[0]) {
            if (strlen($type) > 1) {
                $type = substr($type, 1);
                $this->addType('null');
            } else {
                $type = 'null';
            }
        }

        if (!in_array($type, $this->types)) {
            $this->types[] = $type;
        }
    }
}
