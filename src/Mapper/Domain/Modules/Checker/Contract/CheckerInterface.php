<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Modules\Checker\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;
use PBaszak\UltraMapper\Mapper\Domain\Modules\Checker\Exception\CheckerException;

/**
 * Interface CheckerInterface is used to check if the blueprint
 * is valid. Dedicated checkers are required because of the different
 * problems with blueprints on the tactical levels. For example detecting
 * circular references.
 */
interface CheckerInterface
{
    /**
     * Check if the blueprint is valid. If not, throw an exception.
     *
     * @throws CheckerException if the blueprint is invalid
     */
    public function check(Blueprint $blueprint, Process $process, Context $context): void;
}
