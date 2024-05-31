<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Resolver;

use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;

class ProcessResolver
{
    /**
     * The ProcessResolver is responsible for resolving the process of the mapping.
     *
     * @param TypeInterface $from the type of the source data
     * @param TypeInterface $to   the type of the target data
     *
     * @return string<"normalization"|"denormalization"|"mapping"|"transformation">
     *
     * @throws \LogicException If the process could not be resolved. - Should never happen.
     */
    public function resolve(TypeInterface $from, TypeInterface $to): string
    {
        return match (true) {
            TypeInterface::DENORMALIZED_TYPE === $from->getOriginType()
                && TypeInterface::NORMALIZED_TYPE === $to->getOriginType() => TypeInterface::NORMALIZATION_PROCESS,
            TypeInterface::NORMALIZED_TYPE === $from->getOriginType()
                && TypeInterface::DENORMALIZED_TYPE === $to->getOriginType() => TypeInterface::DENORMALIZATION_PROCESS,
            TypeInterface::DENORMALIZED_TYPE === $from->getOriginType()
                && TypeInterface::DENORMALIZED_TYPE === $to->getOriginType() => TypeInterface::MAPPING_PROCESS,
            TypeInterface::NORMALIZED_TYPE === $from->getOriginType()
                && TypeInterface::NORMALIZED_TYPE === $to->getOriginType() => TypeInterface::TRANSFORMATION_PROCESS,
            default => throw new \LogicException('The process could not be resolved.'),
        };
    }
}
