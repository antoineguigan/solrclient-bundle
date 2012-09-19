<?php

namespace Qimnet\SolrClientBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('qimnet_solr');

        $rootNode
            ->children()
            ->arrayNode('entities')
                ->info('list of entities that should be indexed by the batch command.')
                ->prototype('scalar')
                ->end()
            ->end()
            ->arrayNode('client_options')
                ->info('see the SolrClient documentation on http://php.met for details about the options.')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('port')->defaultValue(8080)->end()
                    ->scalarNode('hostname')->defaultValue('localhost')->end()
                    ->booleanNode('secure')->defaultValue(false)->end()
                    ->scalarNode('path')->end()
                    ->scalarNode('wt')->end()
                    ->scalarNode('login')->end()
                    ->scalarNode('password')->end()
                    ->scalarNode('proxy_host')->end()
                    ->scalarNode('proxy_port')->end()
                    ->scalarNode('proxy_login')->end()
                    ->scalarNode('proxy_password')->end()
                    ->scalarNode('timeout')->end()
                    ->scalarNode('ssl_cert')->end()
                    ->scalarNode('ssl_key')->end()
                    ->scalarNode('ssl_keypassword')->end()
                    ->scalarNode('ssl_cainfo')->end()
                    ->scalarNode('ssl_capath')->end()
                ->end();
        return $treeBuilder;
    }
}
