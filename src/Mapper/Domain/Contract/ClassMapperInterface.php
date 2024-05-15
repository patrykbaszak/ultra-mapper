<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Contract;

use PBaszak\UltraMapper\Mapper\Application\Model\Envelope;

interface ClassMapperInterface
{
    public function map(mixed $data): Envelope;
}
