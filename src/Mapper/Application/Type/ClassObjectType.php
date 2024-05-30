<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Type;

use PBaszak\UltraMapper\Mapper\Application\Contract\TypeInterface;

/**
 * ClassObjectType example:
 * new ClassA (
 *      key1: 'value1',
 *      key2: ['value2'],
 *      key3: new ClassB (
 *         key4: 'value4',
 *     ),
 * )
 */
class ClassObjectType implements TypeInterface
{
    /**
     * The order of the flags matters. If there is more available
     * options, the first one will be used. If some of Your properties
     * are public and some of them not, but their has parameters in the
     * constructor then all of them will be used. The constructor first,
     * and then the public properties will be used to map the data.
     * *If there is no option to get or set some property, then it will be ignored.*.
     *
     * @param class-string|null $overridenBlueprint
     * @param bool              $canUseDirectProperty    Requires public properties. If the property is not public, it will be ignored.
     * @param bool              $canUseConstructor       Requires the parameters with the same name as the properties. You can use
     *                                                   #[TargetProperty] attribute on parameters to map them to the properties.
     * @param bool              $canUseGettersAndSetters #[Accessor] attribute ignores this flag. If `true`, then
     *                                                   UltraMapper will search for the getter and setter methods.
     * @param bool              $canUseReflectionClass   Probably the slowest option but the most universal. Still is fast enough.
     */
    public function __construct(
        /** @var class-string|null */
        protected ?string $overridenBlueprint = null,
        protected bool $canUseDirectProperty = true,
        protected bool $canUseConstructor = true,
        protected bool $canUseGettersAndSetters = false,
        protected bool $canUseReflectionClass = true,
    ) {
        if (!$canUseReflectionClass && !$canUseConstructor && !$canUseDirectProperty && !$canUseGettersAndSetters) {
            throw new \InvalidArgumentException('At least one of the options must be set to true.');
        }
    }

    public function getOverriddenBlueprintClass(): ?string
    {
        return $this->overridenBlueprint;
    }

    public function getOriginType(): string
    {
        return self::DENORMALIZED_TYPE;
    }
}
