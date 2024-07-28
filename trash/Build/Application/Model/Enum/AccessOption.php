<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Model\Enum;

enum AccessOption: string
{
    case METHOD_NAME = 'method';
    case PARAMETER_BLUEPRINT = 'parameter';
}
