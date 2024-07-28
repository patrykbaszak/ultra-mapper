<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Exception;

class AttributeValidationException extends \LogicException
{
    public function __construct(
        string $message,
        int $code,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
