<?php

declare(strict_types=1);

namespace UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Exception\UltraMapperAttributeValidationException;

abstract class UltraMapperAttribute
{
    public const PROCESS_DENORMALIZATION = 1;
    public const PROCESS_NORMALIZATION = 2;
    public const PROCESS_TRANSFORMATION = 4;
    public const PROCESS_MAPPING = 8;

    public const OPTION_GROUPS = 'groups';

    /**
     * @param int                  $processType the process type that the attribute should be applied to
     * @param array<string, mixed> $options     The options used by Your code or the UltraMapper. For example, the `groups` option
     *                                          allows you to specify the groups that the attribute should be applied to.
     */
    protected function __construct(
        protected int $processType = self::PROCESS_DENORMALIZATION | self::PROCESS_NORMALIZATION | self::PROCESS_TRANSFORMATION | self::PROCESS_MAPPING,
        protected array $options = [],
    ) {
    }

    /**
     * Returns the process type.
     *
     * @return int the process type
     */
    public function processType(): int
    {
        return $this->processType;
    }

    /**
     * Returns the options.
     *
     * @return array<string, mixed> the options
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * Validates the attribute.
     *
     * @param \Reflector $reflection the reflection object that the attribute is attached to
     *
     * @throws UltraMapperAttributeValidationException
     */
    abstract public function validate(\Reflector $reflection): void;
}
