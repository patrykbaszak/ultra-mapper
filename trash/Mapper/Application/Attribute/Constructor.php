<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Constructor implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    /**
     * @param int                  $processType If You want to use one static constructor for creation entity and the second for recreates entity
     *                                          from persistence, the best option will be Constructor::MAPPING for creation from a DTO and
     *                                          Constructor::DENORMALIZATION for recreation from persistence. If Your case is more advanced use
     *                                          $options and create Your own BlueprintExtenderStrategy and put it into Extender class in its constructor (as first one).
     * @param array<string, mixed> $options     Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public readonly int $processType = self::DENORMALIZATION | self::MAPPING,
        public readonly array $options = []
    ) {
    }

    public function validate(\Reflector $reflector): void
    {
        // todo implement
        // the method has to be public, static and all parameters has to be matched with properties
    }

    public function getProcessType(): int
    {
        return $this->processType;
    }
}
