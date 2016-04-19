<?php

namespace IPC\SecurityBundle\DependencyInjection;

use IPC\SecurityBundle\Form\Type\LoginType;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * {@inheritdoc}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ipc_security');
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('authentication')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('login')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('form')
                                    ->defaultValue(LoginType::class)
                                ->end()
                                ->scalarNode('view')
                                    ->defaultValue('IPCSecurityBundle:Authentication:login.html.twig')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('doctrine_user_provider')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('username_properties')
                                    ->treatNullLike([])
                                    ->prototype('scalar')->end()
                                    ->defaultValue(['username'])
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}