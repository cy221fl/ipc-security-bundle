<?php

namespace Tests\IPC\SecurityBundle\DependencyInjection;

use IPC\SecurityBundle\DependencyInjection\IPCSecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class IPCSecurityExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Bundle extension
     *
     * @var IPCSecurityExtension
     */
    protected $extension;

    /**
     * Root name of the configuration
     *
     * @var string
     */
    protected $root;

    public function setUp()
    {
        parent::setUp();
        $this->root = 'ipc_security';
        $this->extension = $this->getExtension();
    }

    /**
     * @param array|null $customConfig
     * @return ContainerBuilder
     */
    protected function getCustomizedContainer(array $customConfig = null)
    {
        $config = $this->getConfigYml()[$this->root];
        if (null !== $config && null !== $customConfig) {
            $config = array_merge($config, $customConfig);
        }
        $this->extension->load([$config], $container = $this->getContainerBuilder());
        return $container;
    }

    protected function getConfigYml()
    {
        $ymlFile = __DIR__ . '/../config/config.yml';
        $parser = new Parser();
        return $parser->parse(file_get_contents($ymlFile));
    }

    /**
     * @return IPCSecurityExtension
     */
    protected function getExtension()
    {
        return new IPCSecurityExtension();
    }

    /**
     * @return ContainerBuilder
     */
    protected function getContainerBuilder()
    {
        return new ContainerBuilder();
    }

    public function testDefault()
    {
        $config = $this->getCustomizedContainer(null)->getParameterBag()->all();
        $default = [
            'ipc_security.login.form' => 'IPC\SecurityBundle\Form\Type\LoginType',
            'ipc_security.login.view' => 'IPCSecurityBundle:Security:login.html.twig',
            'ipc_security.login.credentials_expired' => [
                'enabled' => false,
                'route'   => 'credentials_expired',
            ],
            'ipc_security.login.flash_bag' => [
                'enabled'   => false,
                'translate' => true,
                'type'      => [
                    'error' => 'error',
                ],
            ],
            'ipc_security.credentials_expired.form' => 'IPC\SecurityBundle\Form\Type\ChangePasswordType',
            'ipc_security.credentials_expired.view' => 'IPCSecurityBundle:Security:change_password.html.twig',
            'ipc_security.credentials_expired.options' => [
                'require_current'  => false,
                'require_repeated' => true,
            ],
            'ipc_security.credentials_expired.flash_bag' => [
                'enabled'   => false,
                'translate' => true,
                'type'      => [
                    'error'   => 'error',
                    'success' => 'success',
                ],
            ],
            'ipc_security.doctrine_user_provider.entity_class' => 'IPC\SecurityBundle\Entity\User',
            'ipc_security.doctrine_user_provider.username_properties' => [
                'username'
            ],
        ];

        $this->assertEquals($default, $config);
    }
}
