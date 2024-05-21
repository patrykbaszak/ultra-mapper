<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Type;

use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;

/**
 * FlatArrayType example:
 * [
 *      'key1'      => 'value1',
 *      'key2'      => ['value2'],      # eachCollectionIsRoot = true
 *      'key2.0'    => 'value2',        # eachCollectionIsRoot = false
 *      'key3.key4' => 'value4',        # flatArraySeparator = '.'
 *      'key3_key4' => 'value4',        # flatArraySeparator = '_'
 * ]
 */
class FlatArrayType implements TypeInterface
{
    /**
     * @param class-string|null $overridenBlueprint
     * @param string $flatArraySeparator
     * @param bool $eachCollectionIsRoot
     */
    public function __construct(
        /** @var class-string|null */
        protected ?string $overridenBlueprint = null,
        protected string $flatArraySeparator = '.',
        protected bool $eachCollectionIsRoot = false,
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
