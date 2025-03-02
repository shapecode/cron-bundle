<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\DependencyInjection\Compiler;

use RuntimeException;
use Shapecode\Bundle\CronBundle\EventListener\ServiceJobLoaderListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function is_array;

final class CronJobCompilerPass implements CompilerPassInterface
{
    public const CRON_JOB_TAG_ID = 'shapecode_cron.cron_job';

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(ServiceJobLoaderListener::class);

        $tagged = $container->findTaggedServiceIds(self::CRON_JOB_TAG_ID);

        foreach ($tagged as $id => $configs) {
            foreach ($configs as $config) {
                if (! is_array($config)) {
                    throw new RuntimeException('config must be an array', 1740941125172);
                }

                if (! isset($config['expression'])) {
                    throw new RuntimeException('missing expression config', 1653426737628);
                }

                $expression   = $config['expression'];
                $arguments    = $config['arguments'] ?? null;
                $maxInstances = $config['maxInstances'] ?? 1;

                $definition->addMethodCall('addCommand', [
                    $expression,
                    new Reference($id),
                    $arguments,
                    $maxInstances,
                ]);
            }
        }
    }
}
