<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Exception;

class BuilderException extends \LogicException
{
    public function __construct(
        string $message,
        int $code,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
