<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Contract;

interface AttributeInterface
{
    public function validate(\ReflectionProperty|\ReflectionParameter|\ReflectionClass $reflection): void;
}
