<?php

namespace Shapecode\Bundle\CronBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Shapecode\Bundle\CronBundle\DependencyInjection
 * @author  Nikita Loges
 */
class Configuration implements ConfigurationInterface
{

    /** @var string  */
    private const ROOT_NODE = 'shapecode_cron';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(static::ROOT_NODE);

        if (method_exists($treeBuilder, 'getRootNode')) {
            // Symfony 4.2 +
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // Symfony 4.1 and below
            $rootNode = $treeBuilder->root(static::ROOT_NODE);
        }

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
