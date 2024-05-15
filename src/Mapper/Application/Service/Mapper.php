<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Service;

use PBaszak\UltraMapper\Blueprint\Application\Contract\BlueprintInterface;
use PBaszak\UltraMapper\Build\Application\Contract\BuilderInterface;
use PBaszak\UltraMapper\Build\Application\Model\Blueprints;
use PBaszak\UltraMapper\Mapper\Application\Contract\MapperInterface;
use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;
use PBaszak\UltraMapper\Mapper\Application\Model\Envelope;
use PBaszak\UltraMapper\Mapper\Domain\Contract\ClassMapperInterface;
use PBaszak\UltraMapper\Mapper\Domain\Resolver\MapperResolver;

class Mapper implements MapperInterface
{
    // Mapper Settings
    public bool $checkHashesOfDependentFiles = false;

    public function __construct(
        protected BlueprintInterface $blueprint,
        protected BuilderInterface $build,
        protected MapperResolver $mapperResolver,
    ) {
    }

    public function map(
        mixed $data,
        string $blueprintClass,
        TypeInterface $from,
        TypeInterface $to,
        bool $isCollection = false
    ): Envelope {
        $mapper = $this->getMapper($data, $blueprintClass, $from, $to, $isCollection);

        return $mapper->map($data);
    }

    /**
     * Get the mapper for the given options.
     */
    protected function getMapper(
        mixed $data,
        string $blueprintClass,
        TypeInterface $from,
        TypeInterface $to,
        bool $isCollection = false
    ): ClassMapperInterface {
        $shortName = $this->mapperResolver->getMapperShortClassName(...func_get_args());

        // if the blueprint files were changed or the mapper does not exist
        if (
            ($this->checkHashesOfDependentFiles && $this->blueprint->checkIfBlueprintFilesWasChanged($blueprintClass))
            || null === $mapper = $this->mapperResolver->resolve($shortName)
        ) {
            $blueprints = $this->createBlueprints($blueprintClass, $from, $to);

            $build = $this->build->build(
                $shortName,
                $blueprints->origin,
                $blueprints->source,
                $blueprints->target,
                $from,
                $to,
                $isCollection,
            );

            $this->mapperResolver->save($shortName, $build->getMapperFileBody());
            $mapper = $this->mapperResolver->resolve($shortName);
        }

        return $mapper;
    }

    /**
     * Create blueprints for the build.
     *
     * @return {'origin': BlueprintInterface, 'source': BlueprintInterface, 'target': BlueprintInterface}
     */
    protected function createBlueprints(
        string $blueprintClass,
        TypeInterface $from,
        TypeInterface $to
    ): object {
        $originBlueprint = $this->blueprint->createBlueprint($blueprintClass);

        return new Blueprints(
            $originBlueprint,
            $from->getOverriddenBlueprintClass()
                ? $this->blueprint->createBlueprint($from->getOverriddenBlueprintClass())
                : clone $originBlueprint,
            $to->getOverriddenBlueprintClass()
                ? $this->blueprint->createBlueprint($to->getOverriddenBlueprintClass())
                : clone $originBlueprint,
        );
    }
}
