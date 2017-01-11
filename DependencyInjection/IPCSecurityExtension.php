<?php

namespace IPC\SecurityBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages bundle configuration
 */
class IPCSecurityExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'ipc_security.login.form',
            $config['login']['form']
        );
        $container->setParameter(
            'ipc_security.login.view',
            $config['login']['view']
        );
        $container->setParameter(
            'ipc_security.login.credentials_expired',
            $config['login']['credentials_expired']
        );
        $container->setParameter(
            'ipc_security.login.flash_bag',
            $config['login']['flash_bag']
        );

        $container->setParameter(
            'ipc_security.credentials_expired.form',
            $config['credentials_expired']['form']
        );
        $container->setParameter(
            'ipc_security.credentials_expired.view',
            $config['credentials_expired']['view']
        );
        $container->setParameter(
            'ipc_security.credentials_expired.options',
            $config['credentials_expired']['options']
        );
        $container->setParameter(
            'ipc_security.credentials_expired.flash_bag',
            $config['credentials_expired']['flash_bag']
        );

        $container->setParameter(
            'ipc_security.doctrine_user_provider.entity_class',
            $config['doctrine_user_provider']['entity_class']
        );
        $container->setParameter(
            'ipc_security.doctrine_user_provider.username_properties',
            $config['doctrine_user_provider']['username_properties']
        );

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
