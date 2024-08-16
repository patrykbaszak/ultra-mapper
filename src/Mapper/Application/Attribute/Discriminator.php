<?php

declare(strict_types=1);

namespace UltraMapper\Mapper\Application\Attribute;

use Attribute;
use PBaszak\UltraMapper\Mapper\Application\Exception\UltraMapperAttributeValidationException;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
final class Discriminator extends UltraMapperAttribute
{
    public const DISCRIMINATOR_PROPERTY_IN_SAME_CLASS = 1;
    public const DISCRIMINATOR_PROPERTY_IN_PARENT_CLASS = 2;

    /**
     * The discriminator attribute allows You to define how to solve multiple types of objects in one property.
     *
     * @param string                          $property       the property name that will be used as a discriminator
     * @param array<int|string, class-string> $map            the map of the discriminator values to the class names
     * @param int                             $propertySource where to look for the discriminator property
     */
    public function __construct(
        private string $property,
        private array $map,
        private int $propertySource = self::DISCRIMINATOR_PROPERTY_IN_SAME_CLASS,
        int $processType = UltraMapperAttribute::PROCESS_DENORMALIZATION | UltraMapperAttribute::PROCESS_NORMALIZATION | UltraMapperAttribute::PROCESS_TRANSFORMATION | UltraMapperAttribute::PROCESS_MAPPING,
        array $options = [],
    ) {
        parent::__construct($processType, $options);
    }

    public function property(): string
    {
        return $this->property;
    }

    /**
     * Returns the map.
     *
     * @return array<int|string, class-string> the map
     */
    public function map(): array
    {
        return $this->map;
    }

    public function propertySource(): int
    {
        return $this->propertySource;
    }

    public function validate(\Reflector $reflection): void
    {
        if (!$reflection instanceof \ReflectionProperty) {
            throw new UltraMapperAttributeValidationException('The discriminator attribute can only be applied to properties.', 'Make sure that the function which initialize a validation do it correctly.');
        }

        if (!in_array($this->propertySource, [self::DISCRIMINATOR_PROPERTY_IN_SAME_CLASS, self::DISCRIMINATOR_PROPERTY_IN_PARENT_CLASS])) {
            throw new UltraMapperAttributeValidationException('The property source must be either in the same class or in the parent class.', 'Make sure that the property source is either in the same class or in the parent class.');
        }

        if (empty($this->map)) {
            throw new UltraMapperAttributeValidationException('The map must have at least one entry.', 'Make sure that the map has at least one entry.');
        }

        foreach ($this->map as $discriminatorValue => $class) {
            if (!class_exists($class, false)) {
                throw new UltraMapperAttributeValidationException(sprintf('The class %s does not exist.', $class), 'Make sure that the class '.$class.' exists.');
            }

            if (self::DISCRIMINATOR_PROPERTY_IN_SAME_CLASS === $this->propertySource && !property_exists($class, $this->property)) {
                throw new UltraMapperAttributeValidationException(sprintf('The property %s does not exist in the class %s.', $this->property, $class), 'Make sure that the property '.$this->property.' exists in the class '.$class.'.');
            }
        }

        if (self::DISCRIMINATOR_PROPERTY_IN_PARENT_CLASS === $this->propertySource && !property_exists($reflection->getDeclaringClass()->getName(), $this->property)) {
            throw new UltraMapperAttributeValidationException(sprintf('The property %s does not exist in the parent class %s.', $this->property, $reflection->getDeclaringClass()->getName()), 'Make sure that the property '.$this->property.' exists in the class '.$reflection->getDeclaringClass()->getName().'.');
        }
    }
}
