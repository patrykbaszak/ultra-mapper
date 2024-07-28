<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Enum;

enum ClassType: string
{
    case ABSTRACT = 'abstract_class';
    case ENUM = 'enum';
    case INTERFACE = 'interface';
    case STANDARD = 'class';
    case TRAIT = 'trait';
}
