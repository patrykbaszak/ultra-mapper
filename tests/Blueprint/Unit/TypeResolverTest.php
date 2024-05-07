<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit;

use PBaszak\UltraMapper\Blueprint\Application\Enum\TypeDeclaration;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\ClassNotFoundException;
use PBaszak\UltraMapper\Blueprint\Domain\Resolver\TypeResolver;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

#[Group('unit')]
class TypeResolverTest extends TestCase
{
    #[Test]
    public function shouldNotReturnAnyTypes(): void
    {
        $obj = new class() {
            public $property;
        };

        $resolver = (new TypeResolver(new \ReflectionProperty($obj, 'property')))->process();
        $this->assertEmpty($resolver->getTypes());
        $this->assertEmpty($resolver->getInnerTypes());
        $this->assertEquals(TypeDeclaration::UNKNOWN, $resolver->getTypeDeclaration());
    }

    #[Test]
    public function shouldNotReturnAnyTypesBasedOnDocblock(): void
    {
        $obj = new class() {
            /** @var */
            public $property;
        };

        $resolver = (new TypeResolver(new \ReflectionProperty($obj, 'property')))->process();
        $this->assertEmpty($resolver->getTypes());
        $this->assertEmpty($resolver->getInnerTypes());
        $this->assertEquals(TypeDeclaration::UNKNOWN, $resolver->getTypeDeclaration());
    }

    #[Test]
    public function shouldNotReturnAnyParameterTypesBasedOnDocblock(): void
    {
        $obj = new class('') {
            /** @param */
            public function __construct(
                public $property,
            ) {}
        };

        $resolver = (new TypeResolver((new ReflectionMethod($obj, '__construct'))->getParameters()[0]))->process();
        $this->assertEmpty($resolver->getTypes());
        $this->assertEmpty($resolver->getInnerTypes());
        $this->assertEquals(TypeDeclaration::UNKNOWN, $resolver->getTypeDeclaration());
    }

    #[Test]
    public function shouldThrowExceptionOnInvalidDocBlockCollectionClass(): void
    {
        $this->expectException(ClassNotFoundException::class);

        $obj = new class() {
            /** @var Collection<object> */
            public $property;
        };

        (new TypeResolver(new \ReflectionProperty($obj, 'property')))->process();
    }

    #[Test]
    public function shouldThrowExceptionOnInvalidDocBlockInnerCollectionClass(): void
    {
        $this->expectException(ClassNotFoundException::class);

        $obj = new class() {
            /** @var \ArrayObject<object<string>> */
            public $property;
        };

        (new TypeResolver(new \ReflectionProperty($obj, 'property')))->process();
    }

    #[Test]
    public function shouldReturnConstructorParamTypes(): void
    {
        $obj = new class([]) {
            /** @param string[] $property */
            public function __construct(
                public array $property
            ) {
            }
        };

        $resolver = (new TypeResolver((new \ReflectionMethod($obj, '__construct'))->getParameters()[0]))->process();
        $this->assertEquals(['array'], $resolver->getTypes());
        $this->assertEquals(['string' => ['string'], 'int' => ['string']], $resolver->getInnerTypes());
        $this->assertEquals(TypeDeclaration::NAMED, $resolver->getTypeDeclaration());
    }

    #[Test]
    public function shouldReturnConstructorParamTypesBasedOnlyOnReflection(): void
    {
        $obj = [new class([]) {
            public function __construct(
                public array $property
            ) {
            }
        }, new class([]) {
            /** @method __construct */
            public function __construct(
                public array $property
            ) {
            }
        }];

        foreach ($obj as $o) {
            $resolver = (new TypeResolver((new \ReflectionMethod($o, '__construct'))->getParameters()[0]))->process();
            $this->assertEquals(['array'], $resolver->getTypes());
            $this->assertEquals(['string' => ['mixed'], 'int' => ['mixed']], $resolver->getInnerTypes());
            $this->assertEquals(TypeDeclaration::NAMED, $resolver->getTypeDeclaration());
        }
    }

    /** @var BlueprintTest */
    public $property;

    #[Test]
    public function shouldReturnTypeForPropertyClassWithSameNamespace(): void
    {
        $resolver = (new TypeResolver(new \ReflectionProperty($this, 'property')))->process();
        $this->assertEquals(['\\'.BlueprintTest::class], $resolver->getTypes());
        $this->assertEmpty($resolver->getInnerTypes());
    }

    public static function dataProvider(): array
    {
        return [
            // (simple types + nullable simple types) x (based on docblock + based on reflection + based on docblock and reflection)
            'null' => [
                'obj' => new class() {
                    public null $property;
                },
                'expectedTypes' => ['null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'nullBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var null */
                    public $property;
                },
                'expectedTypes' => ['null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'nullBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public null $property;
                },
                'expectedTypes' => ['null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'false' => [
                'obj' => new class() {
                    public false $property;
                },
                'expectedTypes' => ['false'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'falseBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var false */
                    public $property;
                },
                'expectedTypes' => ['false'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'falseBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public false $property;
                },
                'expectedTypes' => ['false'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?false' => [
                'obj' => new class() {
                    public ?false $property;
                },
                'expectedTypes' => ['false', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?falseBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var false|null */
                    public $property;
                },
                'expectedTypes' => ['false', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?falseBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?false $property;
                },
                'expectedTypes' => ['false', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'true' => [
                'obj' => new class() {
                    public true $property;
                },
                'expectedTypes' => ['true'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'trueBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var true */
                    public $property;
                },
                'expectedTypes' => ['true'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'trueBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public true $property;
                },
                'expectedTypes' => ['true'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?true' => [
                'obj' => new class() {
                    public ?true $property;
                },
                'expectedTypes' => ['true', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?trueBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var true|null */
                    public $property;
                },
                'expectedTypes' => ['true', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?trueBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?true $property;
                },
                'expectedTypes' => ['true', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'bool' => [
                'obj' => new class() {
                    public bool $property;
                },
                'expectedTypes' => ['bool'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'boolBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var bool */
                    public $property;
                },
                'expectedTypes' => ['bool'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'boolBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public bool $property;
                },
                'expectedTypes' => ['bool'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?bool' => [
                'obj' => new class() {
                    public ?bool $property;
                },
                'expectedTypes' => ['bool', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?boolBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var bool|null */
                    public $property;
                },
                'expectedTypes' => ['bool', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?boolBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?bool $property;
                },
                'expectedTypes' => ['bool', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'int' => [
                'obj' => new class() {
                    public int $property;
                },
                'expectedTypes' => ['int'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'intBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int */
                    public $property;
                },
                'expectedTypes' => ['int'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'intBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int $property;
                },
                'expectedTypes' => ['int'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?int' => [
                'obj' => new class() {
                    public ?int $property;
                },
                'expectedTypes' => ['int', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?intBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var ?int */
                    public $property;
                },
                'expectedTypes' => ['int', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?intBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?int $property;
                },
                'expectedTypes' => ['int', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'float' => [
                'obj' => new class() {
                    public float $property;
                },
                'expectedTypes' => ['float'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'floatBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var float */
                    public $property;
                },
                'expectedTypes' => ['float'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'floatBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public float $property;
                },
                'expectedTypes' => ['float'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?float' => [
                'obj' => new class() {
                    public ?float $property;
                },
                'expectedTypes' => ['float', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?floatBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var float|null */
                    public $property;
                },
                'expectedTypes' => ['float', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?floatBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?float $property;
                },
                'expectedTypes' => ['float', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'string' => [
                'obj' => new class() {
                    public string $property;
                },
                'expectedTypes' => ['string'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'stringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var string */
                    public $property;
                },
                'expectedTypes' => ['string'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'stringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public string $property;
                },
                'expectedTypes' => ['string'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?string' => [
                'obj' => new class() {
                    public ?string $property;
                },
                'expectedTypes' => ['string', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?stringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var string|null */
                    public $property;
                },
                'expectedTypes' => ['string', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?stringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?string $property;
                },
                'expectedTypes' => ['string', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'object' => [
                'obj' => new class() {
                    public object $property;
                },
                'expectedTypes' => ['object'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'objectBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var object */
                    public $property;
                },
                'expectedTypes' => ['object'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'objectBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public object $property;
                },
                'expectedTypes' => ['object'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?object' => [
                'obj' => new class() {
                    public ?object $property;
                },
                'expectedTypes' => ['object', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?objectBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var object|null */
                    public $property;
                },
                'expectedTypes' => ['object', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?objectBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?object $property;
                },
                'expectedTypes' => ['object', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'DateTime' => [
                'obj' => new class() {
                    public \DateTime $property;
                },
                'expectedTypes' => ['\\DateTime'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?DateTime' => [
                'obj' => new class() {
                    public ?\DateTime $property;
                },
                'expectedTypes' => ['\\DateTime', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\'.Dummy::class => [
                'obj' => new class() {
                    public Dummy $property;
                },
                'expectedTypes' => ['\\'.Dummy::class],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\'.Dummy::class.'BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var Dummy */
                    public $property;
                },
                'expectedTypes' => ['\\'.Dummy::class],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\'.Dummy::class.'BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public Dummy $property;
                },
                'expectedTypes' => ['\\'.Dummy::class],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?\\'.Dummy::class => [
                'obj' => new class() {
                    public ?Dummy $property;
                },
                'expectedTypes' => ['\\'.Dummy::class, 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?\\'.Dummy::class.'BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var Dummy|null */
                    public $property;
                },
                'expectedTypes' => ['\\'.Dummy::class, 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?\\'.Dummy::class.'BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?Dummy $property;
                },
                'expectedTypes' => ['\\'.Dummy::class, 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\'.TypeDeclaration::class => [
                'obj' => new class() {
                    public TypeDeclaration $property;
                },
                'expectedTypes' => ['\\'.TypeDeclaration::class],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\'.TypeDeclaration::class.'BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var TypeDeclaration */
                    public $property;
                },
                'expectedTypes' => ['\\'.TypeDeclaration::class],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\'.TypeDeclaration::class.'BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public TypeDeclaration $property;
                },
                'expectedTypes' => ['\\'.TypeDeclaration::class],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?\\'.TypeDeclaration::class => [
                'obj' => new class() {
                    public ?TypeDeclaration $property;
                },
                'expectedTypes' => ['\\'.TypeDeclaration::class, 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?\\'.TypeDeclaration::class.'BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var TypeDeclaration|null */
                    public $property;
                },
                'expectedTypes' => ['\\'.TypeDeclaration::class, 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            '?\\'.TypeDeclaration::class.'BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?TypeDeclaration $property;
                },
                'expectedTypes' => ['\\'.TypeDeclaration::class, 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'mixed' => [
                'obj' => new class() {
                    public mixed $property;
                },
                'expectedTypes' => ['mixed', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'mixedBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var mixed */
                    public $property;
                },
                'expectedTypes' => ['mixed', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'mixedBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public mixed $property;
                },
                'expectedTypes' => ['mixed', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],

            // special simple types
            'class-stringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var class-string */
                    public $property;
                },
                'expectedTypes' => ['string'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],
            'class-stringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var class-string */
                    public string $property;
                },
                'expectedTypes' => ['string'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::NAMED,
            ],

            // intersection types
            \JsonSerializable::class.'&\\'.Dummy::class => [
                'obj' => new class() {
                    public \JsonSerializable&Dummy $property;
                },
                'expectedTypes' => ['\\'.\JsonSerializable::class, '\\'.Dummy::class],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::INTERSECTION,
            ],
            \JsonSerializable::class.'&\\'.Dummy::class.'BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \JsonSerializable&Dummy */
                    public $property;
                },
                'expectedTypes' => ['\\'.\JsonSerializable::class, '\\'.Dummy::class],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::INTERSECTION,
            ],
            \JsonSerializable::class.'&\\'.Dummy::class.'BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public \JsonSerializable&Dummy $property;
                },
                'expectedTypes' => ['\\'.\JsonSerializable::class, '\\'.Dummy::class],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::INTERSECTION,
            ],
            '?'.\JsonSerializable::class.'&\\'.Dummy::class => [
                'obj' => new class() {
                    public (\JsonSerializable&Dummy)|null $property;
                },
                'expectedTypes' => ['\\'.\JsonSerializable::class, '\\'.Dummy::class, 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::INTERSECTION,
            ],

            // union types
            'false|int' => [
                'obj' => new class() {
                    public int|false $property;
                },
                'expectedTypes' => ['int', 'false'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            'false|intBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|false */
                    public $property;
                },
                'expectedTypes' => ['int', 'false'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            'false|intBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|false $property;
                },
                'expectedTypes' => ['int', 'false'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            '?false|int' => [
                'obj' => new class() {
                    public int|false|null $property;
                },
                'expectedTypes' => ['int', 'false', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            '?false|intBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|false|null */
                    public $property;
                },
                'expectedTypes' => ['int', 'false', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            '?false|intBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|false|null $property;
                },
                'expectedTypes' => ['int', 'false', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            'int|float' => [
                'obj' => new class() {
                    public int|float $property;
                },
                'expectedTypes' => ['int', 'float'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            'int|floatBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|float */
                    public $property;
                },
                'expectedTypes' => ['int', 'float'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            'int|floatBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|float $property;
                },
                'expectedTypes' => ['int', 'float'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            '?int|float' => [
                'obj' => new class() {
                    public int|float|null $property;
                },
                'expectedTypes' => ['int', 'float', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            '?int|floatBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|float|null */
                    public $property;
                },
                'expectedTypes' => ['int', 'float', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            '?int|floatBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|float|null $property;
                },
                'expectedTypes' => ['int', 'float', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            'int|float|string' => [
                'obj' => new class() {
                    public int|float|string $property;
                },
                'expectedTypes' => ['int', 'float', 'string'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            'int|float|stringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|float|string */
                    public $property;
                },
                'expectedTypes' => ['int', 'float', 'string'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            'int|float|stringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|float|string $property;
                },
                'expectedTypes' => ['int', 'float', 'string'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            '?int|float|string' => [
                'obj' => new class() {
                    public int|float|string|null $property;
                },
                'expectedTypes' => ['int', 'float', 'string', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            '?int|float|stringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|float|string|null */
                    public $property;
                },
                'expectedTypes' => ['int', 'float', 'string', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],
            '?int|float|stringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int|float|string|null $property;
                },
                'expectedTypes' => ['int', 'float', 'string', 'null'],
                'expectedInnerTypes' => [],
                'type' => TypeDeclaration::UNION,
            ],

            // collection types
            'array' => [
                'obj' => new class() {
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            'arrayBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array */
                    public $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            'arrayBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '?array' => [
                'obj' => new class() {
                    public ?array $property;
                },
                'expectedTypes' => ['array', 'null'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '?arrayBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array|null */
                    public $property;
                },
                'expectedTypes' => ['array', 'null'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '?arrayBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?array $property;
                },
                'expectedTypes' => ['array', 'null'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\ArrayObject' => [
                'obj' => new class() {
                    public \ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayObject'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\ArrayObjectBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \ArrayObject */
                    public $property;
                },
                'expectedTypes' => ['\\ArrayObject'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\ArrayObjectBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public \ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayObject'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '?\\ArrayObject' => [
                'obj' => new class() {
                    public ?\ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayObject', 'null'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '?\\ArrayObjectBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \ArrayObject|null */
                    public $property;
                },
                'expectedTypes' => ['\\ArrayObject', 'null'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '?\\ArrayObjectBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?\ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayObject', 'null'],
                'expectedInnerTypes' => ['string' => ['mixed'], 'int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],

            // collection with string key
            'array<string, mixed>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, mixed> */
                    public $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            'array<int, mixed>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var array<int, mixed> */
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['int' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '?array<string, mixed>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, mixed>|null */
                    public $property;
                },
                'expectedTypes' => ['array', 'null'],
                'expectedInnerTypes' => ['string' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '?array<string, mixed>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var array<string, mixed>|null */
                    public ?array $property;
                },
                'expectedTypes' => ['array', 'null'],
                'expectedInnerTypes' => ['string' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\ArrayObject<string, mixed>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \ArrayObject<string, mixed> */
                    public $property;
                },
                'expectedTypes' => ['\\ArrayObject'],
                'expectedInnerTypes' => ['string' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\ArrayObject<string, mixed>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var \ArrayObject<string, mixed> */
                    public \ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayObject'],
                'expectedInnerTypes' => ['string' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '?\\ArrayObject<string, mixed>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \ArrayObject<string, mixed>|null */
                    public $property;
                },
                'expectedTypes' => ['\\ArrayObject', 'null'],
                'expectedInnerTypes' => ['string' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],
            '?\\ArrayObject<string, mixed>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var \ArrayObject<string, mixed>|null */
                    public ?\ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayObject', 'null'],
                'expectedInnerTypes' => ['string' => ['mixed']],
                'type' => TypeDeclaration::NAMED,
            ],

            // collection of collection
            'array<string, array<int, string>>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, array<int, string>> */
                    public $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['array']],
                'type' => TypeDeclaration::NAMED,
            ],
            'array<string, array<int, string>>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var array<string, array<int, string>> */
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['array']],
                'type' => TypeDeclaration::NAMED,
            ],

            // collection of union
            'array<string, int|float>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, int|float> */
                    public $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['int', 'float']],
                'type' => TypeDeclaration::NAMED,
            ],
            'array<string, int|float>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var array<string, int|float> */
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['int', 'float']],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\ArrayObject<int|float>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \ArrayObject<int|float> */
                    public $property;
                },
                'expectedTypes' => ['\\ArrayObject'],
                'expectedInnerTypes' => ['string' => ['int', 'float'], 'int' => ['int', 'float']],
                'type' => TypeDeclaration::NAMED,
            ],
            '\\ArrayObject<int|float>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var \ArrayObject<string|int, int|float> */
                    public \ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayObject'],
                'expectedInnerTypes' => ['string' => ['int', 'float'], 'int' => ['int', 'float']],
                'type' => TypeDeclaration::NAMED,
            ],

            // collection of intersection
            'array<string, JsonSerializable&Dummy>BasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, \JsonSerializable&Dummy> */
                    public $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['\\'.\JsonSerializable::class, '\\'.Dummy::class]],
                'type' => TypeDeclaration::NAMED,
            ],
            'array<string, JsonSerializable&Dummy>BasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var array<string, \JsonSerializable&Dummy> */
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['\\'.\JsonSerializable::class, '\\'.Dummy::class]],
                'type' => TypeDeclaration::NAMED,
            ],
        ];
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function testOnDataFromDataProvider(object $obj, array $expectedTypes, array $expectedInnerTypes, ?TypeDeclaration $type = null): void
    {
        $resolver = (new TypeResolver(new \ReflectionProperty($obj, 'property')))->process();
        $types = $resolver->getTypes();
        $innerTypes = $resolver->getInnerTypes();
        sort($expectedTypes);
        ksort($expectedInnerTypes);
        sort($types);
        ksort($innerTypes);

        $this->assertEquals(
            $expectedTypes,
            $types,
            sprintf(
                "\nExpected types:\n\t%s,\nActual types:\n\t%s.\nExpected inner types:\n\t%s,\nActual inner types:\n\t%s.\n",
                json_encode($expectedTypes),
                json_encode($types),
                json_encode($expectedInnerTypes),
                json_encode($innerTypes)
            )
        );
        $this->assertEquals(
            $expectedInnerTypes,
            $innerTypes,
            sprintf(
                "\nExpected types:\n\t%s,\nActual types:\n\t%s.\nExpected inner types:\n\t%s,\nActual inner types:\n\t%s.\n",
                json_encode($expectedTypes),
                json_encode($types),
                json_encode($expectedInnerTypes),
                json_encode($innerTypes),
            )
        );
        $this->assertEquals($type, $resolver->getTypeDeclaration());
    }
}
