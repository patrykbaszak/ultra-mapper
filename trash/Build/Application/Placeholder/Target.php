<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Placeholder;

class Target
{
    public const INIT = '{{target.init}}';
    public const NAME = '{{target.name}}';
    public const PROPERTY_NAME = '{{target.propertyName}}';
    public const DEFAULT_VALUE = '{{target.defaultValue}}';
    public const SETTER = '{{target.setter}}';
}
