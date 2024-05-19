<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Model;

class Build
{
    /** @var array<class-string, object> */
    public array $classes = [];

    public function getMapperFileBody(): string
    {
        throw new \LogicException('Not implemented yet');
    }
}
