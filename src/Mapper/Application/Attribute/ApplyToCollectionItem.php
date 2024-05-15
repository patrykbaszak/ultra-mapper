<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ApplyToCollectionItem implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    public function __construct(
        /** @var object[] */
        public array $attributes = [],
        /** @var array<string, mixed> */
        public array $options = []
    ) {
    }

    /** @param \ReflectionProperty $reflection */
    public function validate(\ReflectionProperty|\ReflectionClass $reflection): void
    {
        // $class = $reflection->getDeclaringClass();

        // if (empty($this->attributes)) {
        //     throw new AttributeException(
        //         sprintf(
        //             'Attribute list is empty. Check the attribute list in the ApplyToCollectionItem attribute. Class: %s, Property: %s.',
        //             $class->getName(),
        //             $reflection->getName()
        //         ),
        //         5951
        //     );
        // }

        // foreach ($this->attributes as $attribute) {
        //     if (!is_object($attribute)) {
        //         throw new AttributeException(
        //             sprintf(
        //                 'Attribute must be an object. Got: %s. Check the attribute list in the ApplyToCollectionItem attribute. Class: %s, Property: %s.',
        //                 gettype($attribute),
        //                 $class->getName(),
        //                 $reflection->getName()
        //             ),
        //             5952
        //         );
        //     }
        // }
    }
}
