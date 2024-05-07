<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Blueprint\Unit\Accessor;

use Attribute;
use PBaszak\UltraMapper\Attribute\ApplyToCollectionItem;
use PBaszak\UltraMapper\Attribute\Ignore;
use PBaszak\UltraMapper\Blueprint\Domain\Accessor\Accessor;
use PBaszak\UltraMapper\Blueprint\Domain\Aggregate\BlueprintAggregate;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use PBaszak\UltraMapper\Tests\Assets\Dummy;
use PBaszak\UltraMapper\Tests\Assets\DummySimple;
use PBaszak\UltraMapper\Tests\Assets\DummySimpleWithAttribute;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class AccessorTest extends TestCase
{
    private static BlueprintAggregate $blueprintAggregate;
    private static Blueprint $blueprint;

    public static function dataProvider(): array
    {
        self::$blueprintAggregate = BlueprintAggregate::create(Dummy::class);
        self::$blueprint = Blueprint::create(DummySimple::class, null);

        $blueprints = self::$blueprintAggregate->blueprints;
        $count = count($blueprints); $key = array_keys($blueprints)[rand(0, $count - 1)];
        $blueprint = $blueprints[$key];

        return [
            [
                $blueprint,
                [
                    'blueprintAggregate' => self::$blueprintAggregate,
                ]
            ],
            [
                self::$blueprint,
                [
                    'blueprintAggregate' => null,
                ]
            ]
        ];
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function shouldReturnResourceForBlueprint(Blueprint $blueprint, array $expected): void
    {
        // getBlueprintAggregate
        $this->assertSame($expected['blueprintAggregate'], (new Accessor($blueprint))->getBlueprintAggregate());

        // getBlueprint
        $this->assertSame($blueprint, (new Accessor($blueprint))->getBlueprint());
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function shouldReturnResourceForMethod(Blueprint $blueprint, array $expected): void
    {
        $methods = $blueprint->methods->methods;
        $count = count($methods); $key = array_keys($methods)[rand(0, $count - 1)];
        $method = $methods[$key];

        // getBlueprintAggregate
        $this->assertSame($expected['blueprintAggregate'], (new Accessor($method))->getBlueprintAggregate());

        // getBlueprint
        $this->assertSame($blueprint, (new Accessor($method))->getBlueprint());
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function shouldReturnResourceForParameter(Blueprint $blueprint, array $expected): void
    {
        $methods = $blueprint->methods->methods;
        $count = count($methods); $key = array_keys($methods)[rand(0, $count - 1)];
        $method = $methods[$key];
        $parameters = $method->parameters->parameters;
        $count = count($parameters); $key = array_keys($parameters)[rand(0, $count - 1)];
        $parameter = $parameters[$key];

        // getBlueprintAggregate
        $this->assertSame($expected['blueprintAggregate'], (new Accessor($parameter))->getBlueprintAggregate());

        // getBlueprint
        $this->assertSame($blueprint, (new Accessor($parameter))->getBlueprint());
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function shouldReturnResourceForParameterType(Blueprint $blueprint, array $expected): void
    {
        $methods = $blueprint->methods->methods;
        $count = count($methods); $key = array_keys($methods)[rand(0, $count - 1)];
        $method = $methods[$key];
        $parameters = $method->parameters->parameters;
        $count = count($parameters); $key = array_keys($parameters)[rand(0, $count - 1)];
        $parameter = $parameters[$key];
        $type = $parameter->type;

        // getBlueprintAggregate
        $this->assertSame($expected['blueprintAggregate'], (new Accessor($type))->getBlueprintAggregate());

        // getBlueprint
        $this->assertSame($blueprint, (new Accessor($type))->getBlueprint());
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function shouldReturnResourceForProperty(Blueprint $blueprint, array $expected): void
    {
        $properties = $blueprint->properties->properties;
        $count = count($properties); $key = array_keys($properties)[rand(0, $count - 1)];
        $property = $properties[$key];

        // getBlueprintAggregate
        $this->assertSame($expected['blueprintAggregate'], (new Accessor($property))->getBlueprintAggregate());

        // getBlueprint
        $this->assertSame($blueprint, (new Accessor($property))->getBlueprint());
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function shouldReturnResourceForPropertyType(Blueprint $blueprint, array $expected): void
    {
        $properties = $blueprint->properties->properties;
        $count = count($properties); $key = array_keys($properties)[rand(0, $count - 1)];
        $property = $properties[$key];
        $type = $property->type;

        // getBlueprintAggregate
        $this->assertSame($expected['blueprintAggregate'], (new Accessor($type))->getBlueprintAggregate());

        // getBlueprint
        $this->assertSame($blueprint, (new Accessor($type))->getBlueprint());
    }

    #[Test]
    public function shouldReturnResourceForPropertyAttribute(): void
    {
        $blueprint = Blueprint::create(DummySimpleWithAttribute::class, null);
        $property = $blueprint->properties->properties['description'];
        $attribute = $property->attributes->attributes[Ignore::class][0];

        // getBlueprintAggregate
        $this->assertSame(null, (new Accessor($attribute))->getBlueprintAggregate());

        // getBlueprint
        $this->assertSame($blueprint, (new Accessor($attribute))->getBlueprint());
    }

    #[Test]
    public function shouldReturnResourceForBlueprintAttribute(): void
    {
        $blueprint = Blueprint::create(ApplyToCollectionItem::class, null);
        $attribute = $blueprint->attributes->attributes[Attribute::class][0];

        // getBlueprintAggregate
        $this->assertSame(null, (new Accessor($attribute))->getBlueprintAggregate());

        // getBlueprint
        $this->assertSame($blueprint, (new Accessor($attribute))->getBlueprint());
    }
}
