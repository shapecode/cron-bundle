<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\DependencyInjection;

use Reflector;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Shapecode\Bundle\CronBundle\DependencyInjection\Compiler\CronJobCompilerPass;
use Shapecode\Bundle\CronBundle\Service\CommandHelper;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class ShapecodeCronExtension extends ConfigurableExtension
{
    /**
     * @param array<mixed> $mergedConfig
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader  = new Loader\YamlFileLoader($container, $locator);
        $loader->load('services.yml');

        $definition = $container->getDefinition(CommandHelper::class);
        $definition->setArgument('$timeout', $mergedConfig['timeout']);

        $container->registerAttributeForAutoconfiguration(
            AsCronJob::class,
            static function (ChildDefinition $definition, AsCronJob $attribute, Reflector $reflector): void {
                $definition->addTag(CronJobCompilerPass::CRON_JOB_TAG_ID, [
                    'expression' => $attribute->schedule,
                    'arguments' => $attribute->arguments,
                    'maxInstances' => $attribute->maxInstances,
                ]);
            }
        );
    }
}
