<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\DependencyInjection\Compiler;

use RuntimeException;
use Shapecode\Bundle\CronBundle\EventListener\ServiceJobLoaderListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CronJobCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(ServiceJobLoaderListener::class);

        $tagged = $container->findTaggedServiceIds('shapecode_cron.cron_job');

        foreach ($tagged as $id => $configs) {
            foreach ($configs as $config) {
                if (! isset($config['expression'])) {
                    throw new RuntimeException('missing expression config');
                }

                $expression = $config['expression'];
                $arguments  = $config['arguments'] ?? null;

                $definition->addMethodCall('addCommand', [
                    $expression,
                    new Reference($id),
                    $arguments,
                ]);
            }
        }
    }
}
