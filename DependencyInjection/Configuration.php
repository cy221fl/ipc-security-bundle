<?php

namespace IPC\SecurityBundle\DependencyInjection;

use IPC\SecurityBundle\Entity\User;
use IPC\SecurityBundle\Form\Type\ChangePasswordType;
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
                ->arrayNode('password')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('change')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('form')
                                    ->defaultValue(ChangePasswordType::class)
                                ->end()
                                ->scalarNode('view')
                                    ->defaultValue('IPCSecurityBundle:Password:change.html.twig')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
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
                                ->booleanNode('handle_expired_credentials')
                                    ->defaultTrue()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('doctrine_user_provider')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('entity_class')
                                    ->defaultValue(User::class)
                                ->end()
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
