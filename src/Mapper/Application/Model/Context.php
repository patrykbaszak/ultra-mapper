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

    /**
     * @param string[] $groups
     * 
     * @return bool Success if the group is matching.
     */
    public function isGroupMatching(array $groups): bool
    {
        $groups = empty($groups) ? ['Default'] : $groups;

        return !empty(array_intersect($this->groups, $groups));
    }
}
