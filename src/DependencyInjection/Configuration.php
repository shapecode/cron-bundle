<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use function method_exists;

class Configuration implements ConfigurationInterface
{
    private const ROOT_NODE = 'shapecode_cron';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder() : TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ROOT_NODE);

        if (method_exists($treeBuilder, 'getRootNode')) {
            // Symfony 4.2 +
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // Symfony 4.1 and below
            $rootNode = $treeBuilder->root(self::ROOT_NODE);
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
