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
     * Mapping denormalized data to normalized data.
     * Like from class object to array. As example: DTO to array.
     */
    public const NORMALIZATION_PROCESS = 'normalization';

    /**
     * Mapping normalized data to denormalized data.
     * From array to class object. As example: array to Entity.
     */
    public const DENORMALIZATION_PROCESS = 'denormalization';

    /**
     * Mapping denormalized data to denormalized data.
     * Like from class object to class object. As example: DTO to Entity.
     */
    public const MAPPING_PROCESS = 'mapping';

    /**
     * Mapping normalized data to normalized data.
     * From array to array, but You can add callbacks or other modifications.
     */
    public const TRANSFORMATION_PROCESS = 'transformation';

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
