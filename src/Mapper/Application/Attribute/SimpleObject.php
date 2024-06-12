<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS)]
class SimpleObject implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    /**
     * @param ?string                   $mainArgumentName       the name of the main argument of the object
     * @param array<string, mixed>|null $otherArgumentsValues   the values of the other arguments of the object
     * @param ?string                   $staticConstructor      the name of the static constructor of the object
     * @param ?string                   $deconstructor          the name of the deconstructor of the object
     * @param array<string, mixed>|null $deconstructorArguments the arguments of the deconstructor of the object
     * @param array<string, mixed>      $options                Options are for modificators of the mapping process. If You need them, You can use them.
     */
    public function __construct(
        public readonly ?string $mainArgumentName = null,
        public readonly ?array $otherArgumentsValues = null,
        public readonly ?string $staticConstructor = null,
        public readonly ?string $deconstructor = null,
        public readonly ?array $deconstructorArguments = null,
        public readonly array $options = []
    ) {
    }

    public function validate(\ReflectionProperty|\ReflectionParameter|\ReflectionClass $reflection): void
    {
        // todo implement
    }
}
