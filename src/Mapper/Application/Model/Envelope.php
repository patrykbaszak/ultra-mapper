<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Model;

use PBaszak\UltraMapper\Mapper\Application\Contract\StampInterface;

class Envelope
{
    /**
     * Envelope is a simple wrapper for the mapped data. It contains stamps that
     * can be used to add additional information to the data, like exceptions.
     *
     * @param StampInterface[] $stamps
     */
    public function __construct(
        public readonly mixed $data,
        /** @var StampInterface[] */
        public array $stamps = [],
    ) {
    }

    /**
     * Method to get the message/data from the envelope.
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Method to get all stamps or only those that are instances of a given class.
     *
     * @param class-string<StampInterface> $filter
     *
     * @return StampInterface[]
     */
    public function getStamps(?string $filter = null): array
    {
        if ($filter) {
            return array_filter($this->stamps, fn ($stamp) => $stamp instanceof $filter);
        }

        return $this->stamps;
    }

    /**
     * Method to add a stamp to the envelope.
     */
    public function addStamp(StampInterface $stamp): self
    {
        $this->stamps[] = $stamp;

        return $this;
    }

    /**
     * Method to remove a stamp from the envelope. You can pass either an object or a class name.
     *
     * @param StampInterface|class-string<StampInterface> $stamp
     */
    public function removeStamp(StampInterface|string $stamp): self
    {
        if (is_string($stamp)) {
            $this->stamps = array_filter($this->stamps, fn ($s) => !$s instanceof $stamp);

            return $this;
        }

        $this->stamps = array_filter($this->stamps, fn ($s) => $s !== $stamp);

        return $this;
    }
}
