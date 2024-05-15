<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Exception;

class ClassNotFoundException extends \LogicException
{
    public function __construct(
        string $message,
        int $code,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
