<?php

declare(strict_types=1);

namespace Tests\Support\UseCases\TransformPersistenceIntoArray\Entity;

enum Country: string
{
    case PL = 'Poland';
    case DE = 'Germany';
    case FR = 'France';
    case ES = 'Spain';
    case IT = 'Italy';
    case UK = 'United Kingdom';
}
