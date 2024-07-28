<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Shared\Application\Exception;

/**
 * Class UltraMapperException is the basic exception for all
 * exceptions thrown by library.
 */
class UltraMapperException extends \Exception
{
    public function __construct(
        string $message,
        string $advice,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        if ('' === $message || '' === $advice) {
            throw new \InvalidArgumentException('Message and advice cannot be empty.');
        }

        parent::__construct(
            sprintf('%s. %s.', $this->format($message), $this->format($advice)),
            $code,
            $previous
        );
    }

    protected function format(string $string): string
    {
        return ucfirst(trim(rtrim($string, '.')));
    }
}
