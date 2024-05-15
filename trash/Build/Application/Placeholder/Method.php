<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Placeholder;

class Method
{
    public const METHOD_NAME = '{{method.name}}';
    public const METHOD_PROPERTY_ASSIGNMENT = '{{method.property_assignment}}';
    public const METHOD_CONSTRUCTOR_ASSIGNMENT = '{{method.constructor_assignment}}';
    public const METHOD_CONSTRUCTOR_PARAMETER_ASSIGNMENT = '{{method.constructor_parameter_assignment}}';
}
