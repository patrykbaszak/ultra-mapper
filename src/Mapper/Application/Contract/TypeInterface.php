<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Contract;

/**
 * Interface TypeInterface
 * You can see that there is no serialization or encoding here. This is because
 * the Ultra Mapper is not responsible for these processes. ;).
 *
 * This interface is used to define the type of the mapping process. It can be
 * `normalized` or `denormalized`. It also defines the process of the mapping:
 * `normalization`, `denormalization`, `mapping` and `transformation`.
 */
interface TypeInterface
{
    public const NORMALIZED_TYPE = 'normalized';
    public const DENORMALIZED_TYPE = 'denormalized';

    /**
     * In some cases You would like to override the default blueprint class for
     * one of the sides of the mapping process. This method allows you to do that.
     *
     * @return class-string|null The class name of the blueprint that should be used
     */
    public function getOverriddenBlueprintClass(): ?string;

    /**
     * Types has their origin type: `normalized` or `denormalized`. This method
     * should return one of these values.
     *
     * @return "normalized"|"denormalized"
     */
    public function getOriginType(): string;
}
