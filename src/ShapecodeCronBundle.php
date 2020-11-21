<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle;

use Shapecode\Bundle\CronBundle\DependencyInjection\Compiler\CronJobCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ShapecodeCronBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CronJobCompilerPass(), PassConfig::TYPE_AFTER_REMOVING);
    }
}
