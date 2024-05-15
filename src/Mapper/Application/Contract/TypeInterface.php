<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Contract;

interface TypeInterface
{
    /**
     * In some cases You would like to override the default blueprint class for
     * one of the sides of the mapping process. This method allows you to do that.
     *
     * @return class-string|null The class name of the blueprint that should be used
     */
    public function getOverriddenBlueprintClass(): ?string;
}
