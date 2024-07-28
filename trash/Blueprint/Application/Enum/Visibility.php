<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Enum;

enum Visibility: string
{
    case PRIVATE = 'private';
    case PROTECTED = 'protected';
    case PUBLIC = 'public';
}
