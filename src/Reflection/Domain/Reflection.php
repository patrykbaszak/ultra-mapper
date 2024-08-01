<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain;

use PBaszak\UltraMapper\Reflection\Domain\Entities\ClassReflection;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionAdded;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionCreated;
use PBaszak\UltraMapper\Reflection\Domain\Events\ReflectionRemoved;
use PBaszak\UltraMapper\Reflection\Domain\Identity\ReflectionId;
use PBaszak\UltraMapper\Shared\Domain\ObjectTypes\AggregateRoot;
use PBaszak\UltraMapper\Shared\Infrastructure\Normalization\Normalizable;

final class Reflection extends AggregateRoot implements Normalizable
{
    private function __construct(
        private ReflectionId $id,
        /** @var class-string|string */
        private string $rootClass,
        /** @var array<class-string|string, ClassReflection> */
        private array $classReflections = [],
    ) {
    }

    /**
     * @param class-string|string $rootClass The class that is being reflected
     * @param string              $idSuffix  It's used to create unique id for reflection
     */
    public static function create(string $rootClass, string $idSuffix = ''): static
    {
        if (!class_exists($rootClass)) {
            throw new \InvalidArgumentException("Class `$rootClass` does not exist.");
        }

        $reflection = new static(
            ReflectionId::create(md5($rootClass).$idSuffix),
            $rootClass,
        );

        $reflection->raise(
            new ReflectionCreated($reflection->id())
        );

        ClassReflection::create($rootClass, $reflection, null);

        return $reflection;
    }

    /**
     * @param class-string|string                         $rootClass
     * @param array<class-string|string, ClassReflection> $classReflections
     */
    public static function recreate(
        ReflectionId $id,
        string $rootClass,
        array $classReflections
    ): static {
        return new static($id, $rootClass, $classReflections);
    }

    public function id(): ReflectionId
    {
        return $this->id;
    }

    /** @return class-string|string */
    public function rootClass(): string
    {
        return $this->rootClass;
    }

    /**
     * @param class-string|string|null $filter
     *
     * @return array<class-string|string, ClassReflection>|ClassReflection
     */
    public function classReflections(?string $filter = null): array|ClassReflection
    {
        if ($filter) {
            if (!array_key_exists($filter, $this->classReflections)) {
                throw new \InvalidArgumentException("Class `$filter` not found in the class reflection collection.");
            }

            return $this->classReflections[$filter];
        }

        return $this->classReflections;
    }

    /**
     * @return true|ClassReflection If the class reflection already exists in the collection, it returns the existing one.
     *                              Otherwise, it returns true to indicate that the class reflection was successfully added.
     */
    public function addClassReflection(ClassReflection $classReflection): true|ClassReflection
    {
        /** @var array<class-string, ClassReflection> */
        $classReflections = $this->classReflections();
        foreach ($classReflections as $existingClassReflection) {
            if ($existingClassReflection->id() === $classReflection->id()) {
                return $existingClassReflection;
            }
        }
        $this->classReflections[$classReflection->name()] = $classReflection;
        $this->raise(new ReflectionAdded($classReflection->id(), ReflectionAdded::CLASS_REFLECTION_ADDED));

        return true;
    }

    public function removeClassReflection(string $classReflectionName): void
    {
        if ($this->rootClass === $classReflectionName) {
            throw new \InvalidArgumentException('Cannot remove the root class reflection.');
        }

        $id = $this->classReflections($classReflectionName)->id();
        unset($this->classReflections[$classReflectionName]);

        $this->raise(
            new ReflectionRemoved(
                $id,
                ReflectionRemoved::CLASS_REFLECTION_REMOVED
            )
        );
    }

    public function normalize(): array
    {
        return [
            'id' => $this->id()->value,
            'rootClass' => $this->rootClass(),
            'classReflections' => array_map(
                fn (ClassReflection $classReflection) => $classReflection->normalize(),
                $this->classReflections()
            ),
        ];
    }

    public static function denormalize(array $data): static
    {
        $classReflections = array_map(
            fn (array $classReflectionData) => ClassReflection::denormalize($classReflectionData),
            $data['classReflections']
        );

        $instance = static::recreate(
            ReflectionId::recreate($data['id']),
            $data['rootClass'],
            $classReflections
        );

        foreach ($instance->classReflections() as $classReflection) {
            $classReflection->root($instance);
        }

        return $instance;
    }
}
