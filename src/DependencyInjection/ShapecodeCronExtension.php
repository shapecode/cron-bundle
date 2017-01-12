<?php

namespace Shapecode\Bundle\CronBundle\DependencyInjection;

use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobInterface;
use Shapecode\Bundle\CronBundle\Entity\Interfaces\CronJobResultInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class ShapecodeCronExtension
 *
 * @package Shapecode\Bundle\CronBundle\DependencyInjection
 * @author  Nikita Loges
 */
class ShapecodeCronExtension extends Extension implements PrependExtensionInterface
{

    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('entity.yml');

        $doctrine['orm'] = [
            'resolve_target_entities' => [
                CronJobInterface::class  => '%shapecode_cron.target_entity_resolver.cron_job.class%',
                CronJobResultInterface::class => '%shapecode_cron.target_entity_resolver.cron_job_result.class%',
            ]
        ];

        $container->prependExtensionConfig('doctrine', $doctrine);
    }
}
