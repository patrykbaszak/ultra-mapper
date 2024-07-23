<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Model\Enum;

enum AssignmentType: int
{
    case SIMPLE = 0; // null, bool, int, float, string, same type
    case SIMPLE_OBJECT = 1; // DateTime from string|array or to string|array
    case CLASS_OBJECT = 2; // new classObject(...$constructorParams)
    case COLLECTION = 3; // foreach ($collection as $index => $item) {}
    case SWITCH = 4; // switch ($data['type']) {case 'example' => ...}
}
