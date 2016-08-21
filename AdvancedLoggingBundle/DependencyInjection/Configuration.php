<?php
namespace AdvancedLoggingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package PhinxBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('advanced_logging');
        $rootNode
            ->children()
                ->arrayNode('log_writers')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->arrayNode('arguments')
                                ->normalizeKeys(false)
                                ->prototype('scalar')->end()
                            ->end()
//                            ->arrayNode('calls')
//                                ->normalizeKeys(false)
//                                ->prototype('array')
//
////                                    ->prototype('array')
////                                        ->prototype('scalar')->end()
////                                        ->prototype('array')->end()
////                                    ->end()
//                                ->end()
//                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

}