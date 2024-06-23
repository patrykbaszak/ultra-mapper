<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Exception;

use PBaszak\UltraMapper\Shared\Application\Exception\UltraMapperException;

class BlueprintException extends UltraMapperException
{
    public function __construct(
        string $message,
        string $advice,
        int $code,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $advice, $code, $previous);
    }
}
