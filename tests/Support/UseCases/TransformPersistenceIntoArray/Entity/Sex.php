<?php

declare(strict_types=1);

namespace Tests\Support\UseCases\TransformPersistenceIntoArray\Entity;

enum Sex: string
{
    case M = 'male';
    case F = 'female';
    case U = 'unknown';
}
