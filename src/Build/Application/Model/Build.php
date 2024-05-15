<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Model;

use PBaszak\UltraMapper\Build\Domain\Entity\Class_;

class Build
{
    /** @var array<class-string, Class_> */
    public array $classes = [];

    public function getMapperFileBody(): string
    {
        throw new \LogicException('Not implemented yet');
    }
}
