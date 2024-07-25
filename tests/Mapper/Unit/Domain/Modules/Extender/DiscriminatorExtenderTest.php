<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Mapper\Unit\Domain\Modules\Checker;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Attribute\Discriminator;
use PBaszak\UltraMapper\Mapper\Application\Model\Context;
use PBaszak\UltraMapper\Mapper\Domain\Matcher\Extender\DiscriminatorExtender;
use PBaszak\UltraMapper\Mapper\Domain\Model\Process;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class DiscriminatorExtenderTest extends TestCase
{
    #[Test]
    public function shouldDiscriminatesSimpleTypes(): void
    {
        $class = new class() {
            public string $type;

            #[Discriminator(['string' => 'string', 'integer' => 'int'], 'type')]
            public $property;
        };
        $blueprint = Blueprint::create(get_class($class));
        
        $discriminator = new DiscriminatorExtender();
        $discriminator->extend($blueprint, new Process([Process::MAPPING_PROCESS]), new Context());

        $types = $blueprint->getRoot()->properties['property']->type->types;
        $this->assertEquals(['string', 'int'], $types);
    }
}
