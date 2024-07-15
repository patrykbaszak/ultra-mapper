<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Modules\Matcher\Exception;

use PBaszak\UltraMapper\Shared\Application\Exception\UltraMapperException;

class PropertyNotMatchedException extends UltraMapperException
{
    public function __construct(
        string $originPropertyPath,
        string $message,
        string $advice,
        int $code = 0,
    ) {
        parent::__construct(
            sprintf(
                'Property "%s" not matched. %s',
                $originPropertyPath,
                $message,
            ),
            $advice,
            $code,
        );
    }
}
