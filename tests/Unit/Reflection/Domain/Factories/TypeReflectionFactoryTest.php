<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Unit\Reflection\Domain\Factories;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\CollectionTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\IntersectionTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\NamedTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\TypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\UnionTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Factories\TypeReflectionFactory;
use PBaszak\UltraMapper\Tests\Support\Assets\Enums\TestEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../Support/Assets/Enums/TestEnum.php';

#[Group('unit')]
class TypeReflectionFactoryTest extends TestCase
{
    #[Test]
    public function shouldReturnMixedType(): void
    {
        $factory = new TypeReflectionFactory();
        $obj = new class() {
            public $property;
        };

        /** @var NamedTypeReflection $reflection */
        $reflection = $factory->createForProperty(new \ReflectionProperty(get_class($obj), 'property'));

        $this->assertEquals('mixed', $reflection->name());
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
}
