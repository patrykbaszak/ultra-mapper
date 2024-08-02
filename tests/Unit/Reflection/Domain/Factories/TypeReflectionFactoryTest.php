<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Unit\Reflection\Domain\Factories;

use PBaszak\UltraMapper\Reflection\Domain\Entities\Type\NamedTypeReflection;
use PBaszak\UltraMapper\Reflection\Domain\Factories\TypeReflectionFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

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
        $reflection = $factory->createForProperty((new \ReflectionProperty(get_class($obj), 'property')));
        
        $this->assertEquals('mixed', $reflection->name());
        $this->assertEquals(0, $reflection->flags());
        $this->assertTrue($reflection->isBuiltIn());
    }
}
