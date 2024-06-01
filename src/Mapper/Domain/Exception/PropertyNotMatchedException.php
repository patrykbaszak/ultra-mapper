<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Exception;

class PropertyNotMatchedException extends \RuntimeException
{
    public function __construct(
        string $originPropertyPath,
        string $message,
        string $advice,
        int $code = 0,
    ) {
        parent::__construct(
            sprintf(
                'Property "%s" not matched. %s %s',
                $originPropertyPath,
                $message,
                $advice,
            ),
            $code,
        );
    }
}
