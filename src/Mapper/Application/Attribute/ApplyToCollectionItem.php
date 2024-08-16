<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use Attribute;
use PBaszak\UltraMapper\Mapper\Application\Exception\UltraMapperAttributeValidationException;
use UltraMapper\Mapper\Application\Attribute\UltraMapperAttribute;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
final class ApplyToCollectionItem extends UltraMapperAttribute
{
    /**
     * The apply to collection item attribute allows you to apply the attribute to the collection item.
     *
     * @param object[] $attributes the attributes that will be applied to the collection item
     */
    public function __construct(
        private array $attributes,
        int $processType = UltraMapperAttribute::PROCESS_DENORMALIZATION | UltraMapperAttribute::PROCESS_NORMALIZATION | UltraMapperAttribute::PROCESS_TRANSFORMATION | UltraMapperAttribute::PROCESS_MAPPING,
        array $options = [],
    ) {
        parent::__construct($processType, $options);
    }

    /**
     * Returns the attributes.
     *
     * @return object[] the attributes
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    public function validate(\Reflector $reflection): void
    {
        if (!$reflection instanceof \ReflectionProperty) {
            throw new UltraMapperAttributeValidationException('The apply to collection item attribute can only be applied to properties.', 'Make sure that the function which initialize a validation do it correctly.');
        }

        if (empty($this->attributes)) {
            throw new UltraMapperAttributeValidationException('The apply to collection item attribute must have at least one attribute.', 'You should remove unused '.__CLASS__.' attribute in '.$reflection->getDeclaringClass()->getName().'::'.$reflection->getName().' property.');
        }

        foreach ($this->attributes as $attribute) {
            if ($attribute instanceof UltraMapperAttribute) {
                $attribute->validate($reflection);
            }
        }
    }
}
