<?php

namespace Shapecode\Bundle\CronBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CronJobCompilerPass
 *
 * @package Shapecode\Bundle\CronBundle\DependencyInjection\Compiler
 * @author  Nikita Loges
 */
class CronJobCompilerPass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('shapecode_cron.cronjob_manager');

        $tagged = $container->findTaggedServiceIds('shapecode_cron.cron_job');

        foreach ($tagged as $id => $values) {
            foreach ($values as $value) {
                $definition->addMethodCall('addJob', [
                    new Reference($id),
                    $value['expression']
                ]);
            }
        }
    }

}
