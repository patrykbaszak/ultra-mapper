<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Type;

use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;

class ObjectType implements TypeInterface
{
    public function getOverriddenBlueprintClass(): ?string
    {
        throw new \LogicException('Not implemented yet');
    }
}
