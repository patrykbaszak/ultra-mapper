<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Groups implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    /** @var string[] */
    public array $groups;

    /**
     * @param string|array<string> $groups  the groups that the property should be included in
     * @param array<string, mixed> $options Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        string|array $groups,
        public int $processType = self::DENORMALIZATION | self::NORMALIZATION | self::TRANSFORMATION | self::MAPPING,
        public array $options = []
    ) {
        $this->groups = is_array($groups) ? $groups : [$groups];
    }

    public function validate(\Reflector $reflector): void
    {
        // todo implement
    }

    public function getProcessType(): int
    {
        return $this->processType;
    }
}
