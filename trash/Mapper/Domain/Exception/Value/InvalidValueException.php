<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Exception\Value;

use PBaszak\UltraMapper\Shared\Application\Exception\UltraMapperException;

class InvalidValueException extends UltraMapperException
{
    public function __construct(
        string $sourcePropertyPath,
        string $targetPropertyPath,
        mixed $value,
        string $message,
        string $advice,
        int $code = 0,
    ) {
        parent::__construct(
            sprintf(
                'Invalid value for property "%s". Property source: "%s". Value: `%s`. %s',
                $targetPropertyPath,
                $sourcePropertyPath,
                var_export($value, true),
                $message,
            ),
            $advice,
            $code,
        );
    }
}
