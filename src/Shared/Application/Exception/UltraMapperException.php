<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Shared\Application\Exception;

class UltraMapperException extends \Exception
{
    public function __construct(
        string $message,
        string $advice,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
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
