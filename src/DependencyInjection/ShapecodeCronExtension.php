<?php

namespace Shapecode\Bundle\CronBundle\DependencyInjection;

use Shapecode\Bundle\CronBundle\Entity as BundleEntities;
use Sonata\AdminBundle\SonataAdminBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class ShapecodeCronExtension
 *
 * @package Shapecode\Bundle\CronBundle\DependencyInjection
 * @author  Nikita Loges
 */
class ShapecodeCronExtension extends ConfigurableExtension implements PrependExtensionInterface
{

    /**
     * @inheritdoc
     */
    public function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if (class_exists(SonataAdminBundle::class)) {
            $loader->load('admin.yml');
        }

        $container->setParameter('shapecode_cron.results.auto_prune', $config['results']['auto_prune']);
        $container->setParameter('shapecode_cron.results.interval', $config['results']['interval']);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'resolve_target_entities' => [
                    BundleEntities\CronJobInterface::class       => BundleEntities\CronJob::class,
                    BundleEntities\CronJobResultInterface::class => BundleEntities\CronJobResult::class,
                ]
            ]
        ]);
    }
}
