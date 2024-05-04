<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Exception;

class ClassNotFoundException extends BlueprintException
{
    public function __construct(
        string $message,
        int $code = 5930,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
