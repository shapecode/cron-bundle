<?php

namespace Shapecode\Bundle\CronBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('shapecode_cron');

        $treeBuilder->getRootNode()
            ->children()
                ->integerNode('timeout')
                    ->defaultNull()
                ->end()
            ->end();
        return $treeBuilder;
    }
}