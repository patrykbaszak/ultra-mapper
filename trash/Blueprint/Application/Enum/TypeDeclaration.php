<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Enum;

enum TypeDeclaration: string
{
    case INTERSECTION = 'intersection';
    case NAMED = 'named';
    case UNION = 'union';
    case UNKNOWN = 'unknown';
}
