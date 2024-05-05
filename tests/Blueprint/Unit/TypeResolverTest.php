<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit;

use ArrayObject;
use PBaszak\UltraMapper\Blueprint\Application\Enum\TypeDeclaration;
use PBaszak\UltraMapper\Blueprint\Domain\Exception\ClassNotFoundException;
use PBaszak\UltraMapper\Blueprint\Domain\Resolver\TypeResolver;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

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
    }

    #[Test]
    public function shouldNotReturnAnyTypesBasedOnDocblock(): void
    {
        $obj = new class() {
            public $property;
        };

        $resolver = (new TypeResolver(new \ReflectionProperty($obj, 'property')))->process();
        $this->assertEmpty($resolver->getTypes());
        $this->assertEmpty($resolver->getInnerTypes());
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
        $this->assertEquals(['string|int' => ['string']], $resolver->getInnerTypes());
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
        }, new class([]) {
            public function __construct(
                public array $property
            ) {
            }
        }];

        foreach ($obj as $o) {
            $resolver = (new TypeResolver((new \ReflectionMethod($o, '__construct'))->getParameters()[0]))->process();
            $this->assertEquals(['array'], $resolver->getTypes());
            $this->assertEquals(['string|int' => ['mixed']], $resolver->getInnerTypes());
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
            // null
            'null' => [
                'obj' => new class() {
                    public null $property;
                },
                'expectedTypes' => ['null'],
                'expectedInnerTypes' => [],
            ],
            'nullBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var null */
                    public $property;
                },
                'expectedTypes' => ['null'],
                'expectedInnerTypes' => [],
            ],
            'nullBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public null $property;
                },
                'expectedTypes' => ['null'],
                'expectedInnerTypes' => [],
            ],

            // boolean
            'boolean' => [
                'obj' => new class() {
                    public bool $property;
                },
                'expectedTypes' => ['bool'],
                'expectedInnerTypes' => [],
            ],
            'booleanBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var bool */
                    public $property;
                },
                'expectedTypes' => ['bool'],
                'expectedInnerTypes' => [],
            ],
            'booleanBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public bool $property;
                },
                'expectedTypes' => ['bool'],
                'expectedInnerTypes' => [],
            ],
            'nullableBoolean' => [
                'obj' => new class() {
                    public ?bool $property;
                },
                'expectedTypes' => ['bool', 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableBooleanBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var bool|null */
                    public $property;
                },
                'expectedTypes' => ['bool', 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableBooleanBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?bool $property;
                },
                'expectedTypes' => ['bool', 'null'],
                'expectedInnerTypes' => [],
            ],

            // integer
            'integer' => [
                'obj' => new class() {
                    public int $property;
                },
                'expectedTypes' => ['int'],
                'expectedInnerTypes' => [],
            ],
            'integerBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int */
                    public $property;
                },
                'expectedTypes' => ['int'],
                'expectedInnerTypes' => [],
            ],
            'integerBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public int $property;
                },
                'expectedTypes' => ['int'],
                'expectedInnerTypes' => [],
            ],
            'nullableInteger' => [
                'obj' => new class() {
                    public ?int $property;
                },
                'expectedTypes' => ['int', 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableIntegerBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var ?int */
                    public $property;
                },
                'expectedTypes' => ['int', 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableIntegerBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?int $property;
                },
                'expectedTypes' => ['int', 'null'],
                'expectedInnerTypes' => [],
            ],

            // float
            'float' => [
                'obj' => new class() {
                    public float $property;
                },
                'expectedTypes' => ['float'],
                'expectedInnerTypes' => [],
            ],
            'floatBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var float */
                    public $property;
                },
                'expectedTypes' => ['float'],
                'expectedInnerTypes' => [],
            ],
            'floatBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public float $property;
                },
                'expectedTypes' => ['float'],
                'expectedInnerTypes' => [],
            ],
            'nullableFloat' => [
                'obj' => new class() {
                    public ?float $property;
                },
                'expectedTypes' => ['float', 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableFloatBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var float|null */
                    public $property;
                },
                'expectedTypes' => ['float', 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableFloatBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?float $property;
                },
                'expectedTypes' => ['float', 'null'],
                'expectedInnerTypes' => [],
            ],

            // string
            'string' => [
                'obj' => new class() {
                    public string $property;
                },
                'expectedTypes' => ['string'],
                'expectedInnerTypes' => [],
            ],
            'stringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var string */
                    public $property;
                },
                'expectedTypes' => ['string'],
                'expectedInnerTypes' => [],
            ],
            'stringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public string $property;
                },
                'expectedTypes' => ['string'],
                'expectedInnerTypes' => [],
            ],
            'nullableString' => [
                'obj' => new class() {
                    public ?string $property;
                },
                'expectedTypes' => ['string', 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableStringBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var string|null */
                    public $property;
                },
                'expectedTypes' => ['string', 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableStringBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?string $property;
                },
                'expectedTypes' => ['string', 'null'],
                'expectedInnerTypes' => [],
            ],

            // array
            'array' => [
                'obj' => new class() {
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],
            'arrayBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array */
                    public $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],
            'arrayBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],
            'nullableArray' => [
                'obj' => new class() {
                    public ?array $property;
                },
                'expectedTypes' => ['array', 'null'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],
            'nullableArrayBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array|null */
                    public $property;
                },
                'expectedTypes' => ['array', 'null'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],
            'nullableArrayBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?array $property;
                },
                'expectedTypes' => ['array', 'null'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],

            // array with value type
            'arrayWithStringValueType' => [
                'obj' => new class() {
                    /** @var string[] */
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string|int' => ['string']],
            ],
            'arrayWithStringValueTypeBasedOnlyOnDocBlock' => [
                'obj' => new class() {
                    /** @var string[] */
                    public $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string|int' => ['string']],
            ],

            // array with key and value types
            'arrayWithStringKeyAndValueType' => [
                'obj' => new class() {
                    /** @var array<string, mixed> */
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['mixed']],
            ],
            'arrayWithStringKeyAndValueTypeBasedOnlyOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, ?array> */
                    public $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['null', 'array']],
            ],
            'arrayWithStringKeyAndArrayAsValueTypeBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var array<string, string[]> */
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string' => ['array']],
            ],

            // ArrayObject
            'ArrayObject' => [
                'obj' => new class() {
                    public \ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayObject'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],
            'ArrayObjectBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var \ArrayObject<mixed> */
                    public $property;
                },
                'expectedTypes' => ['\\ArrayObject'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],
            'ArrayObjectBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    /** @var \ArrayObject<mixed> */
                    public \ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayObject'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],
            'nullableArrayObject' => [
                'obj' => new class() {
                    public ?\ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayObject', 'null'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],

            // object
            'object' => [
                'obj' => new class() {
                    public object $property;
                },
                'expectedTypes' => ['object'],
                'expectedInnerTypes' => [],
            ],
            'objectBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var object */
                    public $property;
                },
                'expectedTypes' => ['object'],
                'expectedInnerTypes' => [],
            ],
            'objectBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public object $property;
                },
                'expectedTypes' => ['object'],
                'expectedInnerTypes' => [],
            ],
            'nullableObject' => [
                'obj' => new class() {
                    public ?object $property;
                },
                'expectedTypes' => ['object', 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableObjectBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var object|null */
                    public $property;
                },
                'expectedTypes' => ['object', 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableObjectBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?object $property;
                },
                'expectedTypes' => ['object', 'null'],
                'expectedInnerTypes' => [],
            ],

            // class
            'class' => [
                'obj' => new class() {
                    public Dummy $property;
                },
                'expectedTypes' => ['\\'.Dummy::class],
                'expectedInnerTypes' => [],
            ],
            'classBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var Dummy */
                    public $property;
                },
                'expectedTypes' => ['\\'.Dummy::class],
                'expectedInnerTypes' => [],
            ],
            'classBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public Dummy $property;
                },
                'expectedTypes' => ['\\'.Dummy::class],
                'expectedInnerTypes' => [],
            ],
            'nullableClass' => [
                'obj' => new class() {
                    public ?Dummy $property;
                },
                'expectedTypes' => ['\\'.Dummy::class, 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableClassBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var Dummy|null */
                    public $property;
                },
                'expectedTypes' => ['\\'.Dummy::class, 'null'],
                'expectedInnerTypes' => [],
            ],
            'nullableClassBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public ?Dummy $property;
                },
                'expectedTypes' => ['\\'.Dummy::class, 'null'],
                'expectedInnerTypes' => [],
            ],

            // DateTime
            'DateTime' => [
                'obj' => new class() {
                    public \DateTime $property;
                },
                'expectedTypes' => ['\\DateTime'],
                'expectedInnerTypes' => [],
            ],
            'nullableDateTime' => [
                'obj' => new class() {
                    public ?\DateTime $property;
                },
                'expectedTypes' => ['\\DateTime', 'null'],
                'expectedInnerTypes' => [],
            ],

            // Complex structure (Array of objects)
            'arrayOfObjects' => [
                'obj' => new class() {
                    /** @var Dummy[] */
                    public array $property;
                },
                'expectedTypes' => ['array'],
                'expectedInnerTypes' => ['string|int' => ['\\'.Dummy::class]],
            ],
            'nullableArrayOfObjects' => [
                'obj' => new class() {
                    /** @var Dummy[]|null */
                    public ?array $property;
                },
                'expectedTypes' => ['array', 'null'],
                'expectedInnerTypes' => ['string|int' => ['\\'.Dummy::class]],
            ],

            // Complex structure (ArrayObject of objects)
            'ArrayObjectOfObjects' => [
                'obj' => new class() {
                    /** @var \ArrayObject<Dummy> */
                    public \ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayObject'],
                'expectedInnerTypes' => ['string|int' => ['\\'.Dummy::class]],
            ],

            // Union types
            'unionTypes' => [
                'obj' => new class() {
                    public int|string $property;
                },
                'expectedTypes' => ['int', 'string'],
                'expectedInnerTypes' => [],
            ],
            'nullableUnionTypes' => [
                'obj' => new class() {
                    public int|string|null $property;
                },
                'expectedTypes' => ['int', 'string', 'null'],
                'expectedInnerTypes' => [],
            ],
            'unionTypesBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|string */
                    public $property;
                },
                'expectedTypes' => ['int', 'string'],
                'expectedInnerTypes' => [],
            ],
            'nullableUnionTypesBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var int|string|null */
                    public $property;
                },
                'expectedTypes' => ['int', 'string', 'null'],
                'expectedInnerTypes' => [],
            ],
            shell_exec('unionTypesBasedOnDocBlockAndReflection') => [
                'obj' => new class() {
                    public int|string $property;
                },
                'expectedTypes' => ['int', 'string'],
                'expectedInnerTypes' => [],
            ],
            shell_exec('unionTypesWithClasses') => [
                'obj' => new class() {
                    public Dummy|\ArrayObject $property;
                },
                'expectedTypes' => ['\\'.Dummy::class, '\\ArrayObject'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],

            // Intersection types
            'intersectionTypes' => [
                'obj' => new class() {
                    public \ArrayAccess&\ArrayObject $property;
                },
                'expectedTypes' => ['\\ArrayAccess', '\\ArrayObject'],
                'expectedInnerTypes' => ['string|int' => ['mixed']],
            ],

            // enum types
            'enumTypes' => [
                'obj' => new class() {
                    public TypeDeclaration $property;
                },
                'expectedTypes' => ['\\'.TypeDeclaration::class],
                'expectedInnerTypes' => [],
            ],
            'nullableEnumTypes' => [
                'obj' => new class() {
                    public ?TypeDeclaration $property;
                },
                'expectedTypes' => ['\\'.TypeDeclaration::class, 'null'],
                'expectedInnerTypes' => [],
            ],
            'enumTypesBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var TypeDeclaration */
                    public $property;
                },
                'expectedTypes' => ['\\'.TypeDeclaration::class],
                'expectedInnerTypes' => [],
            ],
            'nullableEnumTypesBasedOnDocBlock' => [
                'obj' => new class() {
                    /** @var TypeDeclaration|null */
                    public $property;
                },
                'expectedTypes' => ['\\'.TypeDeclaration::class, 'null'],
                'expectedInnerTypes' => [],
            ],
            'enumTypesBasedOnDocBlockAndReflection' => [
                'obj' => new class() {
                    public TypeDeclaration $property;
                },
                'expectedTypes' => ['\\'.TypeDeclaration::class],
                'expectedInnerTypes' => [],
            ],
        ];
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function testOnDataFromDataProvider(object $obj, array $expectedTypes, array $expectedInnerTypes): void
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
    }
}
