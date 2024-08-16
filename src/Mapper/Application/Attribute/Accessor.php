<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use Attribute;
use PBaszak\UltraMapper\Mapper\Application\Exception\UltraMapperAttributeValidationException;
use UltraMapper\Mapper\Application\Attribute\UltraMapperAttribute;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
final class Accessor extends UltraMapperAttribute
{
    public const SETTER = 1;
    public const GETTER = 2;

    /**
     * The accessor attribute allows you to define your own accessor function that will be executed during the mapping process.
     *
     * @param string $method The method name that will be executed. It must be a public method inside same class as property.
     * @param int    $type   The type of the accessor. It can be either a setter or a getter.
     */
    public function __construct(
        private string $method,
        private int $type = self::GETTER,
        int $processType = UltraMapperAttribute::PROCESS_DENORMALIZATION | UltraMapperAttribute::PROCESS_NORMALIZATION | UltraMapperAttribute::PROCESS_TRANSFORMATION | UltraMapperAttribute::PROCESS_MAPPING,
        array $options = [],
    ) {
        parent::__construct($processType, $options);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function type(): int
    {
        return $this->type;
    }

    public function validate(\Reflector $reflection): void
    {
        if (!$reflection instanceof \ReflectionProperty) {
            throw new UltraMapperAttributeValidationException('The accessor attribute can only be applied to properties.', 'Make sure that the function which initialize a validation do it correctly.');
        }

        if (!method_exists($class = $reflection->getDeclaringClass()->getName(), $this->method)) {
            throw new UltraMapperAttributeValidationException(sprintf('The method %s does not exist.', $this->method), 'Make sure that the method '.$this->method.' exists in the class '.$class.'.');
        }

        if (self::SETTER !== $this->type && self::GETTER !== $this->type) {
            throw new UltraMapperAttributeValidationException(sprintf('The type %d is not supported.', $this->type), 'The type must be either a setter or a getter.');
        }
    }
}
