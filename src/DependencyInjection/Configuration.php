<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('shapecode_cron');

        $treeBuilder->getRootNode()
            ->children()
                ->floatNode('timeout')
                    ->defaultNull()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
