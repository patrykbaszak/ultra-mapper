<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\DependencyInjection;

use PBaszak\UltraMapper\UltraMapperBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class UltraMapperExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // do nothing
    }

    public function getAlias(): string
    {
        return UltraMapperBundle::ALIAS;
    }

    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasParameter('PBaszak.ultra_mapper.dev_mode') && true === $container->getParameter('PBaszak.ultra_mapper.dev_mode')) {
            return;
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
