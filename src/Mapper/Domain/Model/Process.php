<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Model;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;

class Process
{
    /**
     * Mapping denormalized data to normalized data.
     * Like from class object to array. As example: DTO to array.
     */
    public const NORMALIZATION_PROCESS = 'normalization';

    /**
     * Mapping normalized data to denormalized data.
     * From array to class object. As example: array to Entity.
     */
    public const DENORMALIZATION_PROCESS = 'denormalization';

    /**
     * Mapping normalized data to normalized data.
     * From array to array, but You can add callbacks or other modifications.
     */
    public const TRANSFORMATION_PROCESS = 'transformation';

    /**
     * Mapping properties between different data.
     * Like from class object to class object. As example: DTO to Entity.
     */
    public const MAPPING_PROCESS = 'mapping';

    public function __construct(
        /** @var string[] $processes */
        public array $processes = []
    ) {
        if (empty($this->processes)) {
            throw new \InvalidArgumentException('The processes array cannot be empty.');
        }

        if (count($this->processes) > 1 && !in_array(self::MAPPING_PROCESS, $this->processes, true)) {
            throw new \InvalidArgumentException('The processes array can have only one element if it does not contain the `mapping` process.');
        }

        if (count($this->processes) > 2) {
            throw new \InvalidArgumentException('The processes array can have only two elements.');
        }

        if (array_diff($this->processes, [
            self::NORMALIZATION_PROCESS,
            self::DENORMALIZATION_PROCESS,
            self::TRANSFORMATION_PROCESS,
            self::MAPPING_PROCESS,
        ])) {
            throw new \InvalidArgumentException('The processes array can contain only the predefined processes.');
        }

        if (array_unique($this->processes) !== $this->processes) {
            throw new \InvalidArgumentException('The processes array cannot contain duplicates.');
        }
    }

    public function count(): int
    {
        return count($this->processes);
    }

    /**
     * Method returns processes, but `mapping` will always last.
     *
     * @return array<string>
     */
    public function getProcesses(): array
    {
        $processes = $this->processes;

        if (in_array(self::MAPPING_PROCESS, $processes, true)) {
            $mapping = array_splice($processes, array_search(self::MAPPING_PROCESS, $processes, true), 1);
            $processes = array_merge($processes, $mapping);
        }

        return $processes;
    }

    public function isAttributeMatchWithProcess(AttributeInterface $attribute): bool
    {
        foreach ($this->processes as $process) {
            $binaryProcessType = $attribute::PROCESS_TYPE_MAP[$process];
            if ($attribute->getProcessType() & $binaryProcessType === $binaryProcessType) {
                return true;
            }
        }

        return false;
    }
}
