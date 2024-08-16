<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use Attribute;
use PBaszak\UltraMapper\Mapper\Application\Exception\UltraMapperAttributeValidationException;
use UltraMapper\Mapper\Application\Attribute\UltraMapperAttribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
final class Constructor extends UltraMapperAttribute
{
    /**
     * The constructor attribute allows You to define static constructor
     * method that will be executed during the mapping process.
     */
    public function __construct(
        int $processType = UltraMapperAttribute::PROCESS_DENORMALIZATION | UltraMapperAttribute::PROCESS_NORMALIZATION | UltraMapperAttribute::PROCESS_TRANSFORMATION | UltraMapperAttribute::PROCESS_MAPPING,
        array $options = [],
    ) {
        parent::__construct($processType, $options);
    }

    public function validate(\Reflector $reflection): void
    {
        if (!$reflection instanceof \ReflectionMethod) {
            throw new UltraMapperAttributeValidationException('The constructor attribute can only be applied to methods.', 'Make sure that the function which initialize a validation do it correctly.');
        }

        if (!$reflection->isStatic()) {
            throw new UltraMapperAttributeValidationException('The constructor attribute can only be applied to static methods.', 'Make sure that the method '.$reflection->getDeclaringClass()->getName().'::'.$reflection->getName().'() is static.');
        }

        if (!$reflection->isPublic()) {
            throw new UltraMapperAttributeValidationException('The constructor attribute can only be applied to public methods.', 'Make sure that the method '.$reflection->getDeclaringClass()->getName().'::'.$reflection->getName().'() is public.');
        }
    }
}
