<?php

namespace Shapecode\Bundle\CronBundle;

use Shapecode\Bundle\CronBundle\DependencyInjection\Compiler\CronJobCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ShapecodeCronBundle
 *
 * @package Shapecode\Bundle\CronBundle
 * @author  Nikita Loges
 */
class ShapecodeCronBundle extends Bundle
{

    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CronJobCompilerPass(), PassConfig::TYPE_AFTER_REMOVING);
    }

}
