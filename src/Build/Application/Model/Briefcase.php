<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Model;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;

class Briefcase
{
    /** @var Assets\ClassCard[] */
    public array $classes = [];

    public function __construct(
        public Blueprint $origin,
        public TypeInterface $from,
        public Blueprint $source,
        public TypeInterface $to,
        public Blueprint $target,
        public bool $isCollection,
    ) {
    }
}
