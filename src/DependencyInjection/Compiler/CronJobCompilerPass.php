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
        $definition = $container->getDefinition('shapecode_cron.event_listener.service_job_loader');

        $tagged = $container->findTaggedServiceIds('shapecode_cron.cron_job');

        foreach ($tagged as $id => $configs) {
            foreach ($configs as $config) {
                if (!$config['expression']) {
                    throw new \RuntimeException('missing expression config');
                }

                $expression = $config['expression'];
                $arguments = (isset($config['arguments'])) ? $config['arguments'] : null;

                $definition->addMethodCall('addCommand', [
                    $expression,
                    new Reference($id),
                    $arguments
                ]);
            }
        }
    }

}
