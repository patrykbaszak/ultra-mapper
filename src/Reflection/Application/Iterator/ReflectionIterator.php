<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Application\Iterator;

use PBaszak\UltraMapper\Reflection\Domain\Entities\AttributeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\ClassReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\MethodReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\ParameterReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\PropertyReflection;
use PBaszak\UltraMapper\Reflection\Domain\Reflection;

class ReflectionIterator
{
    /**
     * @param callable(Reflection, class-string, ClassReflection): void $callback
     */
    public function iterateClassReflections(Reflection $reflection, callable $callback): void
    {
        foreach ($reflection->classReflections() as $class => $classReflection) {
            $callback($reflection, $class, $classReflection);
        }
    }

    /**
     * @param callable(Reflection, ClassReflection, PropertyReflection): void $callback
     */
    public function iterateProperties(Reflection $reflection, callable $callback): void
    {
        $this->iterateClassReflections($reflection, function (Reflection $reflection, string $class, ClassReflection $classReflection) use ($callback) {
            foreach ($classReflection->properties() as $property) {
                $callback($reflection, $classReflection, $property);
            }
        });
    }

    /**
     * @param callable(Reflection, ClassReflection, MethodReflection): void $callback
     */
    public function iterateMethods(Reflection $reflection, callable $callback): void
    {
        $this->iterateClassReflections($reflection, function (Reflection $reflection, string $class, ClassReflection $classReflection) use ($callback) {
            foreach ($classReflection->methods() as $method) {
                $callback($reflection, $classReflection, $method);
            }
        });
    }

    /**
     * @param callable(Reflection, ClassReflection, MethodReflection, ParameterReflection): void $callback
     */
    public function iterateMethodParameters(Reflection $reflection, callable $callback): void
    {
        $this->iterateMethods($reflection, function (Reflection $reflection, ClassReflection $classReflection, MethodReflection $method) use ($callback) {
            foreach ($method->parameters() as $parameter) {
                $callback($reflection, $classReflection, $method, $parameter);
            }
        });
    }

    /**
     * @param callable(Reflection, ClassReflection|MethodReflection|ParameterReflection|PropertyReflection, AttributeReflection): void $callback
     */
    public function iterateAttributes(Reflection $reflection, callable $callback): void
    {
        $this->iterateClassReflections($reflection, function (Reflection $reflection, string $class, ClassReflection $classReflection) use ($callback) {
            foreach ($classReflection->attributes() as $attribute) {
                $callback($reflection, $classReflection, $attribute);
            }

            foreach ($classReflection->methods() as $method) {
                foreach ($method->attributes() as $attribute) {
                    $callback($reflection, $method, $attribute);
                }

                foreach ($method->parameters() as $parameter) {
                    foreach ($parameter->attributes() as $attribute) {
                        $callback($reflection, $parameter, $attribute);
                    }
                }
            }

            foreach ($classReflection->properties() as $property) {
                foreach ($property->attributes() as $attribute) {
                    $callback($reflection, $property, $attribute);
                }
            }
        });
    }
}
