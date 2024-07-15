<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Modules\Extender\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;
use PBaszak\UltraMapper\Mapper\Domain\Modules\Extender\Exception\ExtenderException;

/**
 * Interface ExtenderInterface is used to extend blueprints based
 * on class and properties attributes. For example #[Discriminator]
 * attribute can be used to extend class blueprints with additional
 * classes defined in the map of the discriminator.
 */
interface ExtenderInterface
{
    /**
     * Extend the blueprint with additional classes and properties based
     * on the class and properties attributes.
     *
     * @return bool `true` if the blueprint was extended, `false` otherwise
     *
     * @throws ExtenderException if there was any problem with extending the blueprint
     */
    public function extend(Blueprint $blueprint, Process $process, Context $context): bool;
}
