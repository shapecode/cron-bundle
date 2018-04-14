<?php

namespace Shapecode\Bundle\CronBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Shapecode\Bundle\CronBundle\DependencyInjection
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class Configuration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('shapecode_cron');

        $rootNode
            ->children()
                ->arrayNode('results')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('auto_prune')->defaultTrue()->end()
                            ->scalarNode('interval')->defaultValue('7 days ago')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

}
