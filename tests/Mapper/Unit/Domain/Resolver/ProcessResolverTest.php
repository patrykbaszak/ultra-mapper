<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Mapper\Unit\Domain\Resolver;

use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;
use PBaszak\UltraMapper\Mapper\Application\Type\AnonymousObjectType;
use PBaszak\UltraMapper\Mapper\Application\Type\ArrayType;
use PBaszak\UltraMapper\Mapper\Application\Type\ClassObjectType;
use PBaszak\UltraMapper\Mapper\Application\Type\FlatArrayType;
use PBaszak\UltraMapper\Mapper\Domain\Resolver\ProcessResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class ProcessResolverTest extends TestCase
{
    public static function getDataSet(): array
    {
        return [
            [
                'from' => new ArrayType(),
                'to' => new ArrayType(),
                'expected' => TypeInterface::TRANSFORMATION_PROCESS,
            ],
            [
                'from' => new ArrayType(),
                'to' => new AnonymousObjectType(),
                'expected' => TypeInterface::TRANSFORMATION_PROCESS,
            ],
            [
                'from' => new ArrayType(),
                'to' => new FlatArrayType(),
                'expected' => TypeInterface::TRANSFORMATION_PROCESS,
            ],
            [
                'from' => new ArrayType(),
                'to' => new ClassObjectType(),
                'expected' => TypeInterface::DENORMALIZATION_PROCESS,
            ],

            [
                'from' => new AnonymousObjectType(),
                'to' => new ArrayType(),
                'expected' => TypeInterface::TRANSFORMATION_PROCESS,
            ],
            [
                'from' => new AnonymousObjectType(),
                'to' => new AnonymousObjectType(),
                'expected' => TypeInterface::TRANSFORMATION_PROCESS,
            ],
            [
                'from' => new AnonymousObjectType(),
                'to' => new FlatArrayType(),
                'expected' => TypeInterface::TRANSFORMATION_PROCESS,
            ],
            [
                'from' => new AnonymousObjectType(),
                'to' => new ClassObjectType(),
                'expected' => TypeInterface::DENORMALIZATION_PROCESS,
            ],

            [
                'from' => new FlatArrayType(),
                'to' => new ArrayType(),
                'expected' => TypeInterface::TRANSFORMATION_PROCESS,
            ],
            [
                'from' => new FlatArrayType(),
                'to' => new AnonymousObjectType(),
                'expected' => TypeInterface::TRANSFORMATION_PROCESS,
            ],
            [
                'from' => new FlatArrayType(),
                'to' => new FlatArrayType(),
                'expected' => TypeInterface::TRANSFORMATION_PROCESS,
            ],
            [
                'from' => new FlatArrayType(),
                'to' => new ClassObjectType(),
                'expected' => TypeInterface::DENORMALIZATION_PROCESS,
            ],

            [
                'from' => new ClassObjectType(),
                'to' => new ArrayType(),
                'expected' => TypeInterface::NORMALIZATION_PROCESS,
            ],
            [
                'from' => new ClassObjectType(),
                'to' => new AnonymousObjectType(),
                'expected' => TypeInterface::NORMALIZATION_PROCESS,
            ],
            [
                'from' => new ClassObjectType(),
                'to' => new FlatArrayType(),
                'expected' => TypeInterface::NORMALIZATION_PROCESS,
            ],
            [
                'from' => new ClassObjectType(),
                'to' => new ClassObjectType(),
                'expected' => TypeInterface::MAPPING_PROCESS,
            ],
        ];
    }

    #[Test]
    #[DataProvider('getDataSet')]
    public function testResolve(TypeInterface $from, TypeInterface $to, string $expected): void
    {
        $resolver = new ProcessResolver();
        $result = $resolver->resolve($from, $to);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function testResolveWithInvalidTypes(): void
    {
        $this->expectException(\LogicException::class);
        $mock = $this->createMock(TypeInterface::class);
        $mock->method('getOriginType')->willReturn('invalid');

        $resolver = new ProcessResolver();
        $resolver->resolve(new ArrayType(), $mock);
    }
}
