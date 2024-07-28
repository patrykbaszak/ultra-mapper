<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Model\Enum;

enum AccessType: int
{
    case NONE = 0;
    case ARRAY_GET = 1;
    case ARRAY_SET = 2;
    case ARROW_GET = 3;
    case ARROW_SET = 4;
    case METHOD_GET = 5;
    case METHOD_SET = 6;
    // it means that property has to be connected with parameter of constructor and will be assigned to the array first
    case CONSTRUCTOR = 7;
    // it means that property has to be connected with parameter of static constructor and will be assigned to the array first
    case STATIC_CONSTRUCTOR = 8;
    case REFLECTION_GET = 9;
    case REFLECTION_SET = 10;
}
