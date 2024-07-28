<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Contract;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Build\Application\Exception\BuilderException;
use PBaszak\UltraMapper\Build\Application\Model\Build;
use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;

interface BuilderInterface
{
    /**
     * Method that creates a Build object based on the provided parameters.
     *
     * @param string        $name         The name of the mapper (short name of the class)
     * @param Blueprint     $blueprint    The Blueprint of `origin` with matched properties
     * @param TypeInterface $from         The type of the source data
     * @param TypeInterface $to           The type of the target data
     * @param bool          $isCollection Flag that indicates if the data is a collection
     *
     * @return Build The Build object
     *
     * @throws BuilderException
     */
    public function build(
        string $name,
        Blueprint $blueprint,
        TypeInterface $from,
        TypeInterface $to,
        bool $isCollection,
    ): Build;
}
