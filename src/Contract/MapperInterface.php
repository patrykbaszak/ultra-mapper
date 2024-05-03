<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Contract;

use PBaszak\UltraMapper\Modificator\ModificatorInterface;

interface MapperInterface
{
    /**
     * @param mixed        $data         Data to map
     * @param class-string $blueprint    Blueprint to map data to
     * @param object       $getter       Getters Builder
     * @param object       $setter       Setters Builder
     * @param bool         $isCollection If given data is a collection
     *
     * @return mixed Mapped data
     */
    public function map(
        mixed $data,
        string $blueprint,
        object $getter,
        object $setter,
        bool $isCollection = false
    ): mixed;

    /**
     * Static constructor creates new instance of Mapper without modificators.
     */
    public static function create(): static;

    /**
     * Static constructor creates new instance of Mapper with standard modificators
     * Standard modificators are those that are declared in `PBaszak\UltraMapper\Modificator\UltraMapper\` namespace.
     */
    public static function createWithStandardModificators(): static;

    /**
     * @param ModificatorInterface[] $modificators Modificators to add
     */
    public static function createWithModificators(array $modificators): static;

    /**
     * @param class-string         $modificator Modificator classname
     * @param array<string, mixed> $args        Modificator arguments
     *
     * @throws \InvalidArgumentException If modificator is not instance of PBaszak\UltraMapper\Modificator\ModificatorInterface
     */
    public function addModificator(string $modificator, array $args = []): static;

    /**
     * @param class-string $modificator    Modificator classname
     * @param bool         $throwException Throw exception if modificator is not found
     *
     * @throws \InvalidArgumentException If modificator is not found and $throwException is `true`
     */
    public function removeModificator(string $modificator, bool $throwException = false): static;
}
