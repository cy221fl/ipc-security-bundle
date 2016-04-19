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
            'ipc_security.authentication.login.form',
            $config['authentication']['login']['form']
        );
        $container->setParameter(
            'ipc_security.authentication.login.view',
            $config['authentication']['login']['view']
        );
        $container->setParameter(
            'ipc_security.doctrine_user_provider.username_properties',
            $config['authentication']['doctrine_user_provider']['username_properties']
        );

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
