<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UltraMapperBundle extends Bundle
{
    public const ALIAS = 'pbaszak.ultra_mapper';

    public function getContainerExtension(): ExtensionInterface
    {
        return $this->extension ??= new DependencyInjection\UltraMapperExtension();
    }
}
