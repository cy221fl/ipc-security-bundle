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

    public function testConfiguration()
    {
        $config = $this->getConfigYml()[$this->root];
        $this->extension->load([$config], $container = $this->getContainer());
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
    protected function getContainer()
    {
        return new ContainerBuilder();
    }

}