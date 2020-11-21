<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class ShapecodeCronExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader  = new Loader\YamlFileLoader($container, $locator);
        $loader->load('services.yml');
    }
}
