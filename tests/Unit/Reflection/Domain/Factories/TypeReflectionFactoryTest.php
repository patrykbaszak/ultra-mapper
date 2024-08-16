<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Unit\Reflection\Domain\Factories;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\CollectionTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\IntersectionTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\NamedTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\TypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\UnionTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Factories\TypeReflectionFactory;
use PBaszak\UltraMapper\Tests\Support\Assets\DTO\Dummy;
use PBaszak\UltraMapper\Tests\Support\Assets\Enums\TestEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../Support/Assets/DTO/Dummy.php';
require_once __DIR__.'/../../../../Support/Assets/Enums/TestEnum.php';

#[Group('unit')]
class TypeReflectionFactoryTest extends TestCase
{
    #[Test]
    public function shouldReturnStringTypeForProperty(): void
    {
        $factory = new TypeReflectionFactory();
        $obj = new class() {
            public string $property;
        };

        /** @var NamedTypeReflection $reflection */
        $reflection = $factory->createForProperty(new \ReflectionProperty(get_class($obj), 'property'));

        $this->assertEquals('string', $reflection->name());
        $this->assertEquals(NamedTypeReflection::IS_BUILT_IN, $reflection->flags());
        $this->assertTrue($reflection->isBuiltIn());
    }

    #[Test]
    public function shouldReturnStringTypeForMethod(): void
    {
        $factory = new TypeReflectionFactory();
        $obj = new class() {
            public function test(): string
            {
                return 'test';
            }
        };

        /** @var NamedTypeReflection $reflection */
        $reflection = $factory->createForMethod(new \ReflectionMethod(get_class($obj), 'test'));

        $this->assertEquals('string', $reflection->name());
        $this->assertEquals(NamedTypeReflection::IS_BUILT_IN, $reflection->flags());
        $this->assertTrue($reflection->isBuiltIn());
    }

    #[Test]
    public function shouldReturnStringTypeForPameter(): void
    {
        $factory = new TypeReflectionFactory();
        $obj = new class() {
            public function test(string $parameter): string
            {
                return 'test';
            }
        };

        /** @var NamedTypeReflection $reflection */
        $reflection = $factory->createForParameter((new \ReflectionMethod(get_class($obj), 'test'))->getParameters()[0]);

        $this->assertEquals('string', $reflection->name());
        $this->assertEquals(NamedTypeReflection::IS_BUILT_IN, $reflection->flags());
        $this->assertTrue($reflection->isBuiltIn());
    }

    public static function createTypeReflectionBasedOnReflectionTypeDataProvider(): array
    {
        return [
            'none' => [
                (new \ReflectionProperty(get_class(new class() {
                    public $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
            ],
            'mixed' => [
                (new \ReflectionProperty(get_class(new class() {
                    public mixed $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
            ],
            'null' => [
                (new \ReflectionProperty(get_class(new class() {
                    public null $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
            ],
            'bool' => [
                (new \ReflectionProperty(get_class(new class() {
                    public bool $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create('bool', NamedTypeReflection::IS_BUILT_IN),
            ],
            'int' => [
                (new \ReflectionProperty(get_class(new class() {
                    public int $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
            ],
            'float' => [
                (new \ReflectionProperty(get_class(new class() {
                    public float $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
            ],
            'string' => [
                (new \ReflectionProperty(get_class(new class() {
                    public string $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
            ],
            'array' => [
                (new \ReflectionProperty(get_class(new class() {
                    public array $property;
                }), 'property'))->getType(),
                CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
                ),
            ],
            'iterable' => [
                (new \ReflectionProperty(get_class(new class() {
                    public iterable $property;
                }), 'property'))->getType(),
                CollectionTypeReflection::create(
                    NamedTypeReflection::create('iterable', NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
                ),
            ],
            'object' => [
                (new \ReflectionProperty(get_class(new class() {
                    public object $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN),
            ],
            'nullable' => [
                (new \ReflectionProperty(get_class(new class() {
                    public ?string $property;
                }), 'property'))->getType(),
                UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'union' => [
                (new \ReflectionProperty(get_class(new class() {
                    public int|string $property;
                }), 'property'))->getType(),
                UnionTypeReflection::create([
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'nullable union' => [
                (new \ReflectionProperty(get_class(new class() {
                    public int|string|null $property;
                }), 'property'))->getType(),
                UnionTypeReflection::create([
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'intersection' => [
                (new \ReflectionProperty(get_class(new class() {
                    public \DateTime&\DateTimeInterface $property;
                }), 'property'))->getType(),
                IntersectionTypeReflection::create([
                    NamedTypeReflection::create('DateTime', NamedTypeReflection::IS_CLASS),
                    NamedTypeReflection::create('DateTimeInterface', NamedTypeReflection::IS_INTERFACE),
                ]),
            ],
            'nullable intersection' => [
                (new \ReflectionProperty(get_class(new class() {
                    public (\DateTime&\DateTimeInterface)|null $property;
                }), 'property'))->getType(),
                UnionTypeReflection::create([
                    IntersectionTypeReflection::create([
                        NamedTypeReflection::create('DateTime', NamedTypeReflection::IS_CLASS),
                        NamedTypeReflection::create('DateTimeInterface', NamedTypeReflection::IS_INTERFACE),
                    ]),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'class' => [
                (new \ReflectionProperty(get_class(new class() {
                    public \DateTime $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create('DateTime', NamedTypeReflection::IS_CLASS),
            ],
            'abstract class' => [
                (new \ReflectionProperty(get_class(new class() {
                    public \ReflectionType $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create('ReflectionType', NamedTypeReflection::IS_CLASS | NamedTypeReflection::IS_ABSTRACT),
            ],
            'array access' => [
                (new \ReflectionProperty(get_class(new class() {
                    public \ArrayAccess $property;
                }), 'property'))->getType(),
                CollectionTypeReflection::create(
                    NamedTypeReflection::create('ArrayAccess', NamedTypeReflection::IS_INTERFACE),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
                ),
            ],
            'traversable' => [
                (new \ReflectionProperty(get_class(new class() {
                    public \Traversable $property;
                }), 'property'))->getType(),
                CollectionTypeReflection::create(
                    NamedTypeReflection::create('Traversable', NamedTypeReflection::IS_INTERFACE),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
                ),
            ],
            'enum' => [
                (new \ReflectionProperty(get_class(new class() {
                    public TestEnum $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create(TestEnum::class, NamedTypeReflection::IS_ENUM),
            ],
            'interface' => [
                (new \ReflectionProperty(get_class(new class() {
                    public \DateTimeInterface $property;
                }), 'property'))->getType(),
                NamedTypeReflection::create('DateTimeInterface', NamedTypeReflection::IS_INTERFACE),
            ],
            'spl object storage' => [
                (new \ReflectionProperty(get_class(new class() {
                    public \SplObjectStorage $property;
                }), 'property'))->getType(),
                CollectionTypeReflection::create(
                    NamedTypeReflection::create('SplObjectStorage', NamedTypeReflection::IS_CLASS),
                    NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
        ];
    }

    #[Test]
    #[DataProvider('createTypeReflectionBasedOnReflectionTypeDataProvider')]
    public function testCreateTypeReflectionBasedOnReflectionType(?\ReflectionType $input, TypeReflection $expected): void
    {
        $factory = new TypeReflectionFactory();
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'createTypeReflectionBasedOnReflectionType');

        $result = $method->invokeArgs($factory, [$input]);

        $this->assertEquals($expected, $result);
    }

    public static function createTypeReflectionBasedOnPhpDocumentatorReflectionTypeDataProvider(): array
    {
        return [
            'none' => [
                new \ReflectionProperty(get_class(new class() {
                    public $property;
                }), 'property'),
                NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
            ],
            'mixed' => [
                new \ReflectionProperty(get_class(new class() {
                    public $property;
                }), 'property'),
                NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
            ],
            'null' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var null */
                    public $property;
                }), 'property'),
                NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
            ],
            'bool' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var bool */
                    public $property;
                }), 'property'),
                NamedTypeReflection::create('bool', NamedTypeReflection::IS_BUILT_IN),
            ],
            'int' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var int */
                    public $property;
                }), 'property'),
                NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
            ],
            'float' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var float */
                    public $property;
                }), 'property'),
                NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
            ],
            'string' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var string */
                    public $property;
                }), 'property'),
                NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
            ],
            'array' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var array<int|string, object> */
                    public $property;
                }), 'property'),
                CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN),
                ),
            ],
            'iterable' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var iterable */
                    public $property;
                }), 'property'),
                CollectionTypeReflection::create(
                    NamedTypeReflection::create('iterable', NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
                ),
            ],
            'object' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var object */
                    public $property;
                }), 'property'),
                NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN),
            ],
            'nullable' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var ?string */
                    public $property;
                }), 'property'),
                UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'union' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var int|string */
                    public $property;
                }), 'property'),
                UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'nullable union' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var int|string|null */
                    public $property;
                }), 'property'),
                UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'intersection' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var \DateTime&\DateTimeInterface */
                    public $property;
                }), 'property'),
                IntersectionTypeReflection::create([
                    NamedTypeReflection::create('DateTime', NamedTypeReflection::IS_CLASS),
                    NamedTypeReflection::create('DateTimeInterface', NamedTypeReflection::IS_INTERFACE),
                ]),
            ],
            'nullable intersection' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var (\DateTime&\DateTimeInterface)|null */
                    public $property;
                }), 'property'),
                UnionTypeReflection::create([
                    IntersectionTypeReflection::create([
                        NamedTypeReflection::create('DateTime', NamedTypeReflection::IS_CLASS),
                        NamedTypeReflection::create('DateTimeInterface', NamedTypeReflection::IS_INTERFACE),
                    ]),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'class' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var \DateTime */
                    public $property;
                }), 'property'),
                NamedTypeReflection::create('DateTime', NamedTypeReflection::IS_CLASS),
            ],
            'abstract class' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var \ReflectionType */
                    public $property;
                }), 'property'),
                NamedTypeReflection::create('ReflectionType', NamedTypeReflection::IS_CLASS | NamedTypeReflection::IS_ABSTRACT),
            ],
            'array access' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var \ArrayAccess */
                    public $property;
                }), 'property'),
                CollectionTypeReflection::create(
                    NamedTypeReflection::create('ArrayAccess', NamedTypeReflection::IS_INTERFACE),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
                ),
            ],
            'traversable' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var \Traversable<int,mixed> */
                    public $property;
                }), 'property'),
                CollectionTypeReflection::create(
                    NamedTypeReflection::create('Traversable', NamedTypeReflection::IS_INTERFACE),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
                ),
            ],
            'enum' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var TestEnum */
                    public $property;
                }), 'property'),
                NamedTypeReflection::create(TestEnum::class, NamedTypeReflection::IS_ENUM),
            ],
            'interface' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var \DateTimeInterface */
                    public $property;
                }), 'property'),
                NamedTypeReflection::create('DateTimeInterface', NamedTypeReflection::IS_INTERFACE),
            ],
            'spl object storage' => [
                new \ReflectionProperty(get_class(new class() {
                    /** @var \SplObjectStorage */
                    public $property;
                }), 'property'),
                CollectionTypeReflection::create(
                    NamedTypeReflection::create('SplObjectStorage', NamedTypeReflection::IS_CLASS),
                    NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
        ];
    }

    #[Test]
    #[DataProvider('createTypeReflectionBasedOnPhpDocumentatorReflectionTypeDataProvider')]
    public function testCreateTypeReflectionBasedOnPhpDocumentatorReflectionType(\ReflectionProperty $ref, TypeReflection $expected): void
    {
        $factory = new TypeReflectionFactory();
        $docBlockResolver = new \ReflectionMethod(TypeReflectionFactory::class, 'getDocBlockReflectionTypeFromVarTag');
        $input = $docBlockResolver->invokeArgs($factory, [$ref]);
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'createTypeReflectionBasedOnPhpDocumentatorReflectionType');

        $result = $method->invokeArgs($factory, [$input, new \ReflectionClass(__CLASS__)]);

        $this->assertEquals($expected, $result);
    }

    public static function mergeNamedTypeReflectionsDataProvider(): array
    {
        return [
            [
                null,
                null,
                NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
            ],
            [
                null,
                NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
            ],
            [
                NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                null,
                NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
            ],
            [
                NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
                NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
                NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
            ],
            [
                NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
                NamedTypeReflection::recreate('mixed', NamedTypeReflection::IS_BUILT_IN),
                NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
            ],
            [
                NamedTypeReflection::recreate('mixed', NamedTypeReflection::IS_BUILT_IN),
                NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
                NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
            ],
            [
                NamedTypeReflection::recreate(\DateTimeInterface::class, NamedTypeReflection::IS_INTERFACE),
                NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_CLASS),
                NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_CLASS),
            ],
            [
                NamedTypeReflection::recreate(\ReflectionType::class, NamedTypeReflection::IS_CLASS | NamedTypeReflection::IS_ABSTRACT),
                NamedTypeReflection::recreate(\ReflectionNamedType::class, NamedTypeReflection::IS_CLASS),
                NamedTypeReflection::recreate(\ReflectionNamedType::class, NamedTypeReflection::IS_CLASS),
            ],
            [
                NamedTypeReflection::recreate(\ReflectionNamedType::class, NamedTypeReflection::IS_CLASS),
                NamedTypeReflection::recreate(\ReflectionType::class, NamedTypeReflection::IS_CLASS | NamedTypeReflection::IS_ABSTRACT),
                NamedTypeReflection::recreate(\ReflectionNamedType::class, NamedTypeReflection::IS_CLASS),
            ],
        ];
    }

    #[Test]
    #[DataProvider('mergeNamedTypeReflectionsDataProvider')]
    public function shouldCorrectMergeNamedTypeReflections(?NamedTypeReflection $phpRef, ?NamedTypeReflection $docRef, NamedTypeReflection $expected): void
    {
        $factory = new TypeReflectionFactory();
        $input = [$phpRef, $docRef];
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'mergeNamedTypeReflections');

        $result = $method->invokeArgs($factory, $input);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function shouldThrowLogicExceptionBecausePhpDocumentatorReflectionIsNotCompatibleWithReflectionType(): void
    {
        $factory = new TypeReflectionFactory();
        $input = [
            NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
            NamedTypeReflection::create(\DateTime::class, NamedTypeReflection::IS_CLASS),
        ];
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'mergeNamedTypeReflections');

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(16);
        $method->invokeArgs($factory, $input);
    }

    public static function mergeIntersectionTypeReflectionsDataProvider(): array
    {
        return [
            [
                IntersectionTypeReflection::recreate([
                    NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::recreate(\DateTimeInterface::class, NamedTypeReflection::IS_BUILT_IN),
                ]),
                null,
                IntersectionTypeReflection::recreate([
                    NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::recreate(\DateTimeInterface::class, NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            [
                IntersectionTypeReflection::recreate([
                    NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::recreate(\DateTimeInterface::class, NamedTypeReflection::IS_BUILT_IN),
                ]),
                IntersectionTypeReflection::recreate([
                    NamedTypeReflection::recreate(\Traversable::class, NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::recreate(\ArrayAccess::class, NamedTypeReflection::IS_BUILT_IN),
                ]),
                IntersectionTypeReflection::recreate([
                    NamedTypeReflection::recreate(\Traversable::class, NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::recreate(\ArrayAccess::class, NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
        ];
    }

    #[Test]
    #[DataProvider('mergeIntersectionTypeReflectionsDataProvider')]
    public function shouldCorrectMergeIntersectionTypeReflections(?IntersectionTypeReflection $phpRef, ?IntersectionTypeReflection $docRef, IntersectionTypeReflection $expected): void
    {
        $factory = new TypeReflectionFactory();
        $input = [$phpRef, $docRef];
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'mergeIntersectionTypeReflections');

        $result = $method->invokeArgs($factory, $input);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function shouldThrowLogicExceptionBecauseMergeIntersectionTypeReflectionsDoNotAcceptBothNullArguments(): void
    {
        $factory = new TypeReflectionFactory();
        $input = [
            null,
            null,
        ];
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'mergeIntersectionTypeReflections');

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(17);
        $method->invokeArgs($factory, $input);
    }

    #[Test]
    public function shouldThrowLogicExceptionBecauseMergeIntersectionTypeReflectionsDoNotAcceptArgumentsWhichAreNotIntersectionTypeReflection(): void
    {
        $factory = new TypeReflectionFactory();
        $input = [
            NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
            null,
        ];
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'mergeIntersectionTypeReflections');

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(18);
        $method->invokeArgs($factory, $input);
    }

    public static function UnionTypeReflectionsDataProvider(): array
    {
        return [
            [
                UnionTypeReflection::recreate([
                    NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::recreate(\DateTimeInterface::class, NamedTypeReflection::IS_BUILT_IN),
                ]),
                null,
                UnionTypeReflection::recreate([
                    NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::recreate(\DateTimeInterface::class, NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            [
                UnionTypeReflection::recreate([
                    NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::recreate(\DateTimeInterface::class, NamedTypeReflection::IS_BUILT_IN),
                ]),
                UnionTypeReflection::recreate([
                    NamedTypeReflection::recreate(\Traversable::class, NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::recreate(\ArrayAccess::class, NamedTypeReflection::IS_BUILT_IN),
                ]),
                UnionTypeReflection::recreate([
                    NamedTypeReflection::recreate(\Traversable::class, NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::recreate(\ArrayAccess::class, NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
        ];
    }

    #[Test]
    #[DataProvider('UnionTypeReflectionsDataProvider')]
    public function shouldCorrectMergeUnionTypeReflections(?UnionTypeReflection $phpRef, ?UnionTypeReflection $docRef, UnionTypeReflection $expected): void
    {
        $factory = new TypeReflectionFactory();
        $input = [$phpRef, $docRef];
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'mergeUnionTypeReflections');

        $result = $method->invokeArgs($factory, $input);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function shouldThrowLogicExceptionBecauseMergeUnionTypeReflectionsDoNotAcceptBothNullArguments(): void
    {
        $factory = new TypeReflectionFactory();
        $input = [
            null,
            null,
        ];
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'mergeUnionTypeReflections');

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(17);
        $method->invokeArgs($factory, $input);
    }

    #[Test]
    public function shouldThrowLogicExceptionBecauseMergeUnionTypeReflectionsDoNotAcceptArgumentsWhichAreNotUnionTypeReflection(): void
    {
        $factory = new TypeReflectionFactory();
        $input = [
            NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
            null,
        ];
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'mergeUnionTypeReflections');

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(18);
        $method->invokeArgs($factory, $input);
    }

    public static function CollectionTypeReflectionsDataProvider(): array
    {
        return [
            [
                CollectionTypeReflection::recreate(
                    NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::recreate([
                        NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::recreate('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::recreate('mixed', NamedTypeReflection::IS_BUILT_IN),
                ),
                null,
                CollectionTypeReflection::recreate(
                    NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::recreate([
                        NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::recreate('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::recreate('mixed', NamedTypeReflection::IS_BUILT_IN),
                ),
            ],
            [
                CollectionTypeReflection::recreate(
                    NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::recreate([
                        NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::recreate('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::recreate('mixed', NamedTypeReflection::IS_BUILT_IN),
                ),
                CollectionTypeReflection::recreate(
                    NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::recreate([
                        NamedTypeReflection::recreate('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::recreate('string', NamedTypeReflection::IS_BUILT_IN),
                ),
                CollectionTypeReflection::recreate(
                    NamedTypeReflection::recreate(\DateTime::class, NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::recreate([
                        NamedTypeReflection::recreate('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::recreate('string', NamedTypeReflection::IS_BUILT_IN),
                ),
            ],
        ];
    }

    #[Test]
    #[DataProvider('CollectionTypeReflectionsDataProvider')]
    public function shouldCorrectMergeCollectionTypeReflections(?CollectionTypeReflection $phpRef, ?CollectionTypeReflection $docRef, CollectionTypeReflection $expected): void
    {
        $factory = new TypeReflectionFactory();
        $input = [$phpRef, $docRef];
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'mergeCollectionTypeReflections');

        $result = $method->invokeArgs($factory, $input);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function shouldThrowLogicExceptionBecauseMergeCollectionTypeReflectionsDoNotAcceptBothNullArguments(): void
    {
        $factory = new TypeReflectionFactory();
        $input = [
            null,
            null,
        ];
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'mergeCollectionTypeReflections');

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(17);
        $method->invokeArgs($factory, $input);
    }

    #[Test]
    public function shouldThrowLogicExceptionBecauseMergeCollectionTypeReflectionsDoNotAcceptArgumentsWhichAreNotCollectionTypeReflection(): void
    {
        $factory = new TypeReflectionFactory();
        $input = [
            NamedTypeReflection::recreate('int', NamedTypeReflection::IS_BUILT_IN),
            null,
        ];
        $method = new \ReflectionMethod(TypeReflectionFactory::class, 'mergeCollectionTypeReflections');

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(18);
        $method->invokeArgs($factory, $input);
    }

    public static function dataProvider(): array
    {
        return [
            // (simple types + nullable simple types) x (based on docblock + based on reflection + based on docblock and reflection)
            'null' => [
                'obj' => new class() {
                    public null $property;
                },
                'expected' => NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
            ],
            'nullBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var null */
                    public $property;
                },
                'expected' => NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
            ],
            'nullBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public null $property;
                },
                'expected' => NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
            ],
            'false' => [
                'obj' => new class() {
                    public false $property;
                },
                'expected' => NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
            ],
            'falseBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var false */
                    public $property;
                },
                'expected' => NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
            ],
            'falseBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public false $property;
                },
                'expected' => NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
            ],
            '?false' => [
                'obj' => new class() {
                    public ?false $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?falseBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var false|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?falseBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?false $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'true' => [
                'obj' => new class() {
                    public true $property;
                },
                'expected' => NamedTypeReflection::create('true', NamedTypeReflection::IS_BUILT_IN),
            ],
            'trueBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var true */
                    public $property;
                },
                'expected' => NamedTypeReflection::create('true', NamedTypeReflection::IS_BUILT_IN),
            ],
            'trueBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public true $property;
                },
                'expected' => NamedTypeReflection::create('true', NamedTypeReflection::IS_BUILT_IN),
            ],
            '?true' => [
                'obj' => new class() {
                    public ?true $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('true', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?trueBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var true|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('true', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?trueBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?true $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('true', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'bool' => [
                'obj' => new class() {
                    public bool $property;
                },
                'expected' => NamedTypeReflection::create('bool', NamedTypeReflection::IS_BUILT_IN),
            ],
            'boolBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var bool */
                    public $property;
                },
                'expected' => NamedTypeReflection::create('bool', NamedTypeReflection::IS_BUILT_IN),
            ],
            'boolBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public bool $property;
                },
                'expected' => NamedTypeReflection::create('bool', NamedTypeReflection::IS_BUILT_IN),
            ],
            '?bool' => [
                'obj' => new class() {
                    public ?bool $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('bool', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?boolBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var bool|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('bool', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?boolBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?bool $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('bool', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'int' => [
                'obj' => new class() {
                    public int $property;
                },
                'expected' => NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
            ],
            'intBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int */
                    public $property;
                },
                'expected' => NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
            ],
            'intBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int $property;
                },
                'expected' => NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
            ],
            '?int' => [
                'obj' => new class() {
                    public ?int $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?intBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var ?int */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?intBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?int $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'float' => [
                'obj' => new class() {
                    public float $property;
                },
                'expected' => NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
            ],
            'floatBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var float */
                    public $property;
                },
                'expected' => NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
            ],
            'floatBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public float $property;
                },
                'expected' => NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
            ],
            '?float' => [
                'obj' => new class() {
                    public ?float $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?floatBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var float|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?floatBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?float $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'string' => [
                'obj' => new class() {
                    public string $property;
                },
                'expected' => NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
            ],
            'stringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var string */
                    public $property;
                },
                'expected' => NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
            ],
            'stringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public string $property;
                },
                'expected' => NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
            ],
            '?string' => [
                'obj' => new class() {
                    public ?string $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?stringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var string|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?stringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?string $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'object' => [
                'obj' => new class() {
                    public object $property;
                },
                'expected' => NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN),
            ],
            'objectBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var object */
                    public $property;
                },
                'expected' => NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN),
            ],
            'objectBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public object $property;
                },
                'expected' => NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN),
            ],
            '?object' => [
                'obj' => new class() {
                    public ?object $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?objectBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var object|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?objectBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?object $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('object', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'DateTime' => [
                'obj' => new class() {
                    public \DateTime $property;
                },
                'expected' => NamedTypeReflection::create('DateTime', NamedTypeReflection::IS_CLASS),
            ],
            '?DateTime' => [
                'obj' => new class() {
                    public ?\DateTime $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('DateTime', NamedTypeReflection::IS_CLASS),
                ]),
            ],
            '\\'.Dummy::class => [
                'obj' => new class() {
                    public Dummy $property;
                },
                'expected' => NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
            ],
            '\\'.Dummy::class.'BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var Dummy */
                    public $property;
                },
                'expected' => NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
            ],
            '\\'.Dummy::class.'BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public Dummy $property;
                },
                'expected' => NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
            ],
            '?\\'.Dummy::class => [
                'obj' => new class() {
                    public ?Dummy $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
                ]),
            ],
            '?\\'.Dummy::class.'BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var Dummy|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?\\'.Dummy::class.'BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?Dummy $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
                ]),
            ],
            '\\'.TestEnum::class => [
                'obj' => new class() {
                    public TestEnum $property;
                },
                'expected' => NamedTypeReflection::create(TestEnum::class, NamedTypeReflection::IS_ENUM),
            ],
            '\\'.TestEnum::class.'BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var TestEnum */
                    public $property;
                },
                'expected' => NamedTypeReflection::create(TestEnum::class, NamedTypeReflection::IS_ENUM),
            ],
            '\\'.TestEnum::class.'BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public TestEnum $property;
                },
                'expected' => NamedTypeReflection::create(TestEnum::class, NamedTypeReflection::IS_ENUM),
            ],
            '?\\'.TestEnum::class => [
                'obj' => new class() {
                    public ?TestEnum $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create(TestEnum::class, NamedTypeReflection::IS_ENUM),
                ]),
            ],
            '?\\'.TestEnum::class.'BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var TestEnum|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create(TestEnum::class, NamedTypeReflection::IS_ENUM),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?\\'.TestEnum::class.'BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?TestEnum $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create(TestEnum::class, NamedTypeReflection::IS_ENUM),
                ]),
            ],
            'mixed' => [
                'obj' => new class() {
                    public mixed $property;
                },
                'expected' => NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
            ],
            'mixedBasedOnDocBlock' => [
                'obj' => new class() {
                    public $property;
                },
                'expected' => NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
            ],
            'mixedBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public mixed $property;
                },
                'expected' => NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN),
            ],

            // special simple types
            'class-stringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var class-string */
                    public $property;
                },
                'expected' => NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
            ],
            'class-stringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var class-string */
                    public string $property;
                },
                'expected' => NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
            ],

            // intersection types
            \JsonSerializable::class.'&\\'.Dummy::class => [
                'obj' => new class() {
                    public \JsonSerializable&Dummy $property;
                },
                'expected' => IntersectionTypeReflection::create([
                    NamedTypeReflection::create('JsonSerializable', NamedTypeReflection::IS_INTERFACE),
                    NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
                ]),
            ],
            \JsonSerializable::class.'&\\'.Dummy::class.'BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \JsonSerializable&Dummy */
                    public $property;
                },
                'expected' => IntersectionTypeReflection::create([
                    NamedTypeReflection::create('JsonSerializable', NamedTypeReflection::IS_INTERFACE),
                    NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
                ]),
            ],
            \JsonSerializable::class.'&\\'.Dummy::class.'BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public \JsonSerializable&Dummy $property;
                },
                'expected' => IntersectionTypeReflection::create([
                    NamedTypeReflection::create('JsonSerializable', NamedTypeReflection::IS_INTERFACE),
                    NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
                ]),
            ],
            '?'.\JsonSerializable::class.'&\\'.Dummy::class => [
                'obj' => new class() {
                    public (\JsonSerializable&Dummy)|null $property;
                },
                'expected' => UnionTypeReflection::create([
                    IntersectionTypeReflection::create([
                        NamedTypeReflection::create('JsonSerializable', NamedTypeReflection::IS_INTERFACE),
                        NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
                    ]),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],

            // union types
            'false|int' => [
                'obj' => new class() {
                    public int|false $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'false|intBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|false */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'false|intBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|false $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?false|int' => [
                'obj' => new class() {
                    public int|false|null $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?false|intBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|false|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?false|intBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|false|null $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('false', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'int|float' => [
                'obj' => new class() {
                    public int|float $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'int|floatBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|float */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'int|floatBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|float $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?int|float' => [
                'obj' => new class() {
                    public int|float|null $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?int|floatBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|float|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?int|floatBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|float|null $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'int|float|string' => [
                'obj' => new class() {
                    public int|float|string $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'int|float|stringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|float|string */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            'int|float|stringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|float|string $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?int|float|string' => [
                'obj' => new class() {
                    public int|float|string|null $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?int|float|stringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|float|string|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?int|float|stringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|float|string|null $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],

            // collection types
            'array' => [
                'obj' => new class() {
                    public array $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
            'arrayBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array */
                    public $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
            'arrayBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public array $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
            '?array' => [
                'obj' => new class() {
                    public ?array $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                        UnionTypeReflection::create([
                            NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                            NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        ]),
                        NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                    ),
                ]),
            ],
            '?arrayBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                        UnionTypeReflection::create([
                            NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                            NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        ]),
                        NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                    ),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?arrayBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var ?array */
                    public ?array $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                        UnionTypeReflection::create([
                            NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                            NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        ]),
                        NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                    ),
                ]),
            ],
            '\\ArrayObject' => [
                'obj' => new class() {
                    public \ArrayObject $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
            '\\ArrayObjectBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \ArrayObject */
                    public $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
            '\\ArrayObjectBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var \ArrayObject<int, mixed> */
                    public \ArrayObject $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
            '?\\ArrayObject' => [
                'obj' => new class() {
                    public ?\ArrayObject $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                        UnionTypeReflection::create([
                            NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                            NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        ]),
                        NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                    ),
                ]),
            ],
            '?\\ArrayObjectBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \ArrayObject|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                        UnionTypeReflection::create([
                            NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                            NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        ]),
                        NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                    ),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?\\ArrayObjectBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var ?\ArrayObject<string, string> */
                    public ?\ArrayObject $property;
                },
                'expected' => UnionTypeReflection::create([
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN)
                    ),
                ]),
            ],

            // collection with string key
            'array<string, mixed>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, mixed> */
                    public $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
            'array<int, mixed>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var array<int, mixed> */
                    public array $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
            '?array<string, mixed>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, mixed>|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                    ),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?array<string, mixed>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var array<string, mixed>|null */
                    public ?array $property;
                },
                'expected' => UnionTypeReflection::create([
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                    ),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '\\ArrayObject<string, mixed>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \ArrayObject<string, mixed> */
                    public $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
            '\\ArrayObject<string, mixed>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var \ArrayObject<string, mixed> */
                    public \ArrayObject $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                ),
            ],
            '?\\ArrayObject<string, mixed>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \ArrayObject<string, mixed>|null */
                    public $property;
                },
                'expected' => UnionTypeReflection::create([
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                    ),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],
            '?\\ArrayObject<string, mixed>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var \ArrayObject<string, mixed>|null */
                    public ?\ArrayObject $property;
                },
                'expected' => UnionTypeReflection::create([
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('mixed', NamedTypeReflection::IS_BUILT_IN)
                    ),
                    NamedTypeReflection::create('null', NamedTypeReflection::IS_BUILT_IN),
                ]),
            ],

            // collection of collection
            'array<string, array<int, string>>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, array<int, string>> */
                    public $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN)
                    )
                ),
            ],
            'array<string, array<int, string>>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var array<string, array<int, string>> */
                    public array $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    CollectionTypeReflection::create(
                        NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN)
                    )
                ),
            ],

            // collection of union
            'array<string, int|float>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, int|float> */
                    public $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    ])
                ),
            ],
            'array<string, int|float>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var array<string, int|float> */
                    public array $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    ])
                ),
            ],
            '\\ArrayObject<int|float>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \ArrayObject<int|float> */
                    public $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    ])
                ),
            ],
            '\\ArrayObject<int|float>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var \ArrayObject<string|int, int|float> */
                    public \ArrayObject $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('ArrayObject', NamedTypeReflection::IS_CLASS),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                    ]),
                    UnionTypeReflection::create([
                        NamedTypeReflection::create('int', NamedTypeReflection::IS_BUILT_IN),
                        NamedTypeReflection::create('float', NamedTypeReflection::IS_BUILT_IN),
                    ])
                ),
            ],

            // collection of intersection
            'array<string, JsonSerializable&Dummy>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, \JsonSerializable&Dummy> */
                    public $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    IntersectionTypeReflection::create([
                        NamedTypeReflection::create('JsonSerializable', NamedTypeReflection::IS_INTERFACE),
                        NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
                    ])
                ),
            ],
            'array<string, JsonSerializable&Dummy>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var array<string, \JsonSerializable&Dummy> */
                    public array $property;
                },
                'expected' => CollectionTypeReflection::create(
                    NamedTypeReflection::create('array', NamedTypeReflection::IS_BUILT_IN),
                    NamedTypeReflection::create('string', NamedTypeReflection::IS_BUILT_IN),
                    IntersectionTypeReflection::create([
                        NamedTypeReflection::create('JsonSerializable', NamedTypeReflection::IS_INTERFACE),
                        NamedTypeReflection::create(Dummy::class, NamedTypeReflection::IS_CLASS),
                    ])
                ),
            ],
        ];
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function testOnDataFromDataProvider(object $obj, TypeReflection $expected): void
    {
        $ref = new \ReflectionProperty($obj, 'property');
        $factory = new TypeReflectionFactory();

        $result = $factory->createForProperty($ref);

        $this->assertEquals($expected, $result);
    }
}
