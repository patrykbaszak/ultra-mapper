<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Unit\Shared\Application\Exception;

use PBaszak\UltraMapper\Shared\Application\Exception\UltraMapperException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class UltraMapperExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new UltraMapperException('message', 'advice', 1);

        self::assertEquals('Message. Advice.', $exception->getMessage());
        self::assertEquals(1, $exception->getCode());
    }

    public function testConstructWithoutCode(): void
    {
        $exception = new UltraMapperException('message', 'advice');

        self::assertEquals('Message. Advice.', $exception->getMessage());
        self::assertEquals(0, $exception->getCode());
    }

    public function testConstructWithoutAdvice(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new UltraMapperException('message', '');
    }

    public function testConstructWithoutMessage(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new UltraMapperException('', 'advice');
    }

    public function testConstructWithoutMessageAndAdvice(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new UltraMapperException('', '');
    }
}
