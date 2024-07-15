<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Service;

use PBaszak\UltraMapper\Blueprint\Application\Contract\BlueprintInterface;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Build\Application\Contract\BuilderInterface;
use PBaszak\UltraMapper\Mapper\Application\Contract\MapperInterface;
use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Application\Model\Envelope;
use PBaszak\UltraMapper\Mapper\Domain\Contract\ClassMapperInterface;
use PBaszak\UltraMapper\Mapper\Domain\Modules;
use PBaszak\UltraMapper\Mapper\Domain\Resolver\MapperResolver;
use PBaszak\UltraMapper\Mapper\Domain\Resolver\ProcessResolver;

class Mapper implements MapperInterface
{
    /*
     * Mapper configuration. You should setup this here or (if You use Symfony)
     * in the dedicated configuration file.
     */

    /*
     * If the blueprint files were changed, the mapper will be recreated.
     * Production should have this set to `false`.
     */
    public bool $checkHashesOfDependentFiles = false;

    /*
     * All errors are stored in the `Envelope` object. If `true` and the mapping
     * error occurs, the exception will be thrown. If you want to get the errors,
     * set this to `false` and check the `Envelope` object.
     */
    public bool $throwExceptionWhenMappingError = false;

    public function __construct(
        protected MapperResolver $mapperResolver,
        protected BlueprintInterface $blueprint,

        // Build module - required for the mapper creation
        protected BuilderInterface $builder,
        // blueprint preparation modules - required for the mapper creation
        protected Modules\Checker\Contract\CheckerInterface $checker,
        protected Modules\Extender\Contract\ExtenderInterface $extender,
        protected Modules\Matcher\Contract\MatcherInterface $matcher,
        protected Modules\Modificator\Contract\ModificatorInterface $modificator,
    ) {
    }

    public function map(
        mixed $data,
        mixed &$output,
        string $blueprintClass,
        TypeInterface $from,
        TypeInterface $to,
        Context $context = new Context(),
    ): Envelope {
        $mapper = $this->getMapper($blueprintClass, $from, $to, $context);

        return $mapper->map($data, $output);
    }

    /**
     * Get the mapper for the given options.
     */
    protected function getMapper(
        string $blueprintClass,
        TypeInterface $from,
        TypeInterface $to,
        Context $context,
    ): ClassMapperInterface {
        $shortName = $this->mapperResolver->getMapperShortClassName(...func_get_args(), ...$this->modificator->getModifiers());

        // if the blueprint files were changed or the mapper does not exist
        if (
            ($this->checkHashesOfDependentFiles && $this->blueprint->checkIfBlueprintFilesWasChanged($blueprintClass))
            || null === $mapper = $this->mapperResolver->resolve($shortName)
        ) {
            $blueprints = $this->createBlueprints($blueprintClass, $from, $to);
            $processType = (new ProcessResolver())->resolve($from, $to);

            foreach ($blueprints as $processUse => $blueprint) {
                do {
                    $hasExtended = $this->extender->extend($blueprint, $processType, $context);
                    $hasModified = $this->modificator->modify($blueprint, $processType, $context, $processUse);
                } while ($hasExtended || $hasModified);

                $this->checker->check($blueprint, $processType, $context);
            }

            $this->matcher->matchBlueprints($context, $processType, ...$blueprints);

            // not implemented yet
            // $build = $this->build->build(
            //     $shortName,
            //     $blueprints,
            //     $from,
            //     $to,
            //     $isCollection,
            // );

            // $this->mapperResolver->save($shortName, $build->getMapperFileBody());
            $mapper = $this->mapperResolver->resolve($shortName);
        }

        return $mapper;
    }

    /**
     * Create blueprints for the build.
     *
     * @return array{'origin': Blueprint, 'source': Blueprint, 'target': Blueprint}
     */
    protected function createBlueprints(
        string $blueprintClass,
        TypeInterface $from,
        TypeInterface $to
    ): array {
        $originBlueprint = $this->blueprint->createBlueprint($blueprintClass);

        return [
            self::BLUEPRINT_PROCESS_USE => $originBlueprint,
            self::FROM_PROCESS_USE => $from->getOverriddenBlueprintClass()
                ? $this->blueprint->createBlueprint($from->getOverriddenBlueprintClass())
                : clone $originBlueprint,
            self::TO_PROCESS_USE => $to->getOverriddenBlueprintClass()
                ? $this->blueprint->createBlueprint($to->getOverriddenBlueprintClass())
                : clone $originBlueprint,
        ];
    }
}
