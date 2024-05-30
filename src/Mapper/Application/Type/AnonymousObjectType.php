<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Type;

use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;

/**
 * AnonymousObjectType example:
 * (object) [
 *      'key1'      => 'value1',
 *      'key2'      => ['value2'],
 *      'key3'      => (object) [
 *          'key4'  => 'value4',
 *      ]
 * ]
 */
class AnonymousObjectType implements TypeInterface
{
    /**
     * @param class-string|null $overridenBlueprint
     */
    public function __construct(
        /** @var class-string|null */
        protected ?string $overridenBlueprint = null,
    ) {
    }

    public function getOverriddenBlueprintClass(): ?string
    {
        return $this->overridenBlueprint;
    }

    public function getOriginType(): string
    {
        return self::NORMALIZED_TYPE;
    }
}
