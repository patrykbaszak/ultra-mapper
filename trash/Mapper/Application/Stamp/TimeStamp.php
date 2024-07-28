<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Stamp;

use PBaszak\UltraMapper\Mapper\Application\Contract\StampInterface;

class TimeStamp implements StampInterface
{
    public function __construct(
        public readonly string $name,
        public readonly int|float|\DateTime $value
    ) {
    }

    public function compareValues(TimeStamp $stamp): int|float
    {
        if (gettype($this->value) !== gettype($stamp->value)) {
            throw new \LogicException('Cannot compare values of different types.');
        }

        if ($this->value instanceof \DateTime) {
            return $this->value->getTimestamp() - $stamp->value->getTimestamp();
        }

        return $this->value - $stamp->value;
    }
}
