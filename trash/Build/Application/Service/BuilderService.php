<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Service;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Build\Application\Contract\BuilderInterface;
use PBaszak\UltraMapper\Build\Application\Model\Build;
use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;

class BuilderService implements BuilderInterface
{
    public function build(
        string $name,
        Blueprint $blueprint,
        TypeInterface $from,
        TypeInterface $to,
        bool $isCollection
    ): Build {
        throw new \Exception('Method not implemented');
    }
}
