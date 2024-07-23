<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Build\Application\Model;

use PBaszak\UltraMapper\Build\Application\Model\Enum\AccessOption;
use PBaszak\UltraMapper\Build\Application\Model\Enum\AccessType;

class PropertyBuildContext
{
    public ?AccessType $accessType = null;
    /** @var array<AccessOption, mixed> */
    public array $accessOptions = [];
}
