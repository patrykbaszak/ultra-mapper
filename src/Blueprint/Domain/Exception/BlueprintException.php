<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Exception;

/**
 * Every exception thrown by Blueprint should be logic exception.
 */
class BlueprintException extends \LogicException
{
    public function __construct(
        string $message,
        int $code = 5920,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
