<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Model;

class Context
{
    /**
     * @param string[] $groups
     */
    public function __construct(
        /** @var string[] $groups */
        public array $groups = ['Default'],
        public bool $isCollection = false,
    ) {
    }
}
