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
        $rootNode    = $treeBuilder->root('ipc_security');
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('login')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('form')
                            ->defaultValue(LoginType::class)
                        ->end()
                        ->scalarNode('view')
                            ->defaultValue('IPCSecurityBundle:Security:login.html.twig')
                        ->end()
                        ->arrayNode('credentials_expired')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->scalarNode('route')
                                    ->defaultValue('credentials_expired')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('flash_bag')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->booleanNode('translate')
                                    ->defaultTrue()
                                ->end()
                                ->arrayNode('type')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('error')
                                            ->defaultValue('error')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('credentials_expired')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('form')
                            ->defaultValue(ChangePasswordType::class)
                        ->end()
                        ->scalarNode('view')
                            ->defaultValue('IPCSecurityBundle:Security:change_password.html.twig')
                        ->end()
                        ->arrayNode('options')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('require_current')
                                    ->defaultFalse()
                                ->end()
                                ->booleanNode('require_repeated')
                                    ->defaultTrue()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('flash_bag')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->booleanNode('translate')
                                    ->defaultTrue()
                                ->end()
                                ->arrayNode('type')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('error')
                                            ->defaultValue('error')
                                        ->end()
                                        ->scalarNode('success')
                                            ->defaultValue('success')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
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
        ;
        return $treeBuilder;
    }
}
