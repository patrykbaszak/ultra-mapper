<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Modules\Modificator\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;
use PBaszak\UltraMapper\Mapper\Domain\Modules\Modifier\Exception\ModifierException;

/**
 * Interface ModificatorInterface is used to modify blueprints based
 * on class and properties attributes. For example Symfony's `#[Assert\NotBlank]`
 * attribute can be translated to the `#[Callback]` attribute in the blueprint.
 */
interface ModificatorInterface
{
    /**
     * Get the list of modifiers. It's required by MapperService
     * to create hash of mapping class unique for the given modifiers.
     *
     * @return ModifierInterface[]
     */
    public function getModifiers(): array;

    /**
     * Based on modifiers, modify the blueprint to support attributes of
     * external libraries like symfony serializer or doctrine ORM.
     *
     * @param "origin"|"source"|"target" $processUse
     *
     * @return bool `True` if the blueprint was extended, `false` otherwise
     *
     * @throws ModifierException
     */
    public function modify(Blueprint $blueprint, Process $process, Context $context, string $processUse): bool;
}
