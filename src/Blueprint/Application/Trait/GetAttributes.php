<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Application\Trait;

use PBaszak\UltraMapper\Blueprint\Application\Model\Assets\AttributeBlueprint;
use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;

trait GetAttributes
{
    /**
     * @return object[] array of attribute instances
     */
    public function getAttributes(string $class, ?string $process = null): array
    {
        /** @var array<AttributeBlueprint> $attrs */
        $attrs = $this->attributes[$class] ?? [];

        /** @var object[] $attrs */
        $attrs = array_map(fn (AttributeBlueprint $attr): object => $attr->newInstance(), $attrs);

        if (!$process) {
            return $attrs;
        }

        foreach ($attrs as $index => $attr) {
            if ($attr instanceof AttributeInterface) {
                $binaryProcessType = $attr::PROCESS_TYPE_MAP[$process];
                if (($attr->getProcessType() & $binaryProcessType) !== $binaryProcessType) {
                    unset($attrs[$index], $attr);
                }
            }
        }

        return array_values($attrs);
    }
}
