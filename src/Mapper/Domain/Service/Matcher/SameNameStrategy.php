<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Domain\Service\Matcher;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\PropertyBlueprint;

class SameNameStrategy implements MatchingStrategyInterface
{
    public function confirmPropertiesMatching(string $processType, PropertyBlueprint $origin, PropertyBlueprint $source, PropertyBlueprint $target): bool
    {
        return $origin->getName() === $source->getName() && $origin->getName() === $target->getName();
    }
}
