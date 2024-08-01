<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Reflection\Domain\Entities\Interfaces;

use PBaszak\UltraMapper\Reflection\Domain\Entities\AttributeReflection;

interface AttributesSupport
{
    /**
     * @param class-string|null $filter
     *
     * @return array<class-string, AttributeReflection[]>|AttributeReflection[]
     */
    public function attributes(?string $filter = null): array;

    public function addAttribute(AttributeReflection $attribute): void;

    public function removeAttribute(AttributeReflection $attribute): void;
}
