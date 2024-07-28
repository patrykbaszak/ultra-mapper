<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Resolver;

use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;

class ProcessResolver
{
    /**
     * The ProcessResolver is responsible for resolving the process of the mapping.
     *
     * @param TypeInterface $from the type of the source data
     * @param TypeInterface $to   the type of the target data
     *
     * @throws \LogicException If the process could not be resolved. - Should never happen.
     */
    public function resolve(TypeInterface $from, TypeInterface $to): Process
    {
        $mainProcess = match (true) {
            TypeInterface::DENORMALIZED_TYPE === $from->getOriginType()
                && TypeInterface::NORMALIZED_TYPE === $to->getOriginType() => Process::NORMALIZATION_PROCESS,
            TypeInterface::NORMALIZED_TYPE === $from->getOriginType()
                && TypeInterface::DENORMALIZED_TYPE === $to->getOriginType() => Process::DENORMALIZATION_PROCESS,
            TypeInterface::DENORMALIZED_TYPE === $from->getOriginType()
                && TypeInterface::DENORMALIZED_TYPE === $to->getOriginType() => Process::MAPPING_PROCESS,
            TypeInterface::NORMALIZED_TYPE === $from->getOriginType()
                && TypeInterface::NORMALIZED_TYPE === $to->getOriginType() => Process::TRANSFORMATION_PROCESS,
            default => throw new \LogicException('The process could not be resolved.'),
        };

        if (
            Process::MAPPING_PROCESS !== $mainProcess
            && (null !== $from->getOverriddenBlueprintClass()
                || null !== $to->getOverriddenBlueprintClass())
        ) {
            return new Process([$mainProcess, Process::MAPPING_PROCESS]);
        }

        return new Process([$mainProcess]);
    }
}
