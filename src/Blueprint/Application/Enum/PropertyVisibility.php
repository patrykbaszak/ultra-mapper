<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Enum;

enum PropertyVisibility: string
{
    case PRIVATE = 'private';
    case PROTECTED = 'protected';
    case PUBLIC = 'public';
}
