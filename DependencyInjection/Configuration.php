<?php

namespace SymfonyContrib\Bundle\AlerterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('alerter');

        $rootNode->children()
            ->arrayNode('alerters')
                ->defaultValue([])
                ->useAttributeAsKey('name')
                ->prototype('scalar')->end()
            ->end()
            ->arrayNode('data_points')
                ->defaultValue([])
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children()
                        ->scalarNode('service')
                            ->isRequired()
                        ->end()
                        ->scalarNode('method')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
