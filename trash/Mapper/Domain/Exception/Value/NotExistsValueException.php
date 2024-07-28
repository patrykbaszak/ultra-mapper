<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Exception\Value;

use PBaszak\UltraMapper\Shared\Application\Exception\UltraMapperException;

class NotExistsValueException extends UltraMapperException
{
    public function __construct(
        string $sourcePropertyPath,
        string $targetPropertyPath,
        string $message,
        string $advice,
        int $code = 0,
    ) {
        parent::__construct(
            sprintf(
                'Not exists value for property "%s". Property source: "%s". %s',
                $sourcePropertyPath,
                $targetPropertyPath,
                $message,
            ),
            $advice,
            $code,
        );
    }
}
