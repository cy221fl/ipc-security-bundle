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
        //$options = $container->getParameter($this->root);
    }

//    public function testCustomSoapOptions()
//    {
//        $config = [];
//        $this->extension->load([$config], $container = $this->getContainer());
//        $this->assertNull($container->getParameter('hocaras_client.adapter.soap.wsdl'));
//
//        $soapOptions = $container->getParameter('hocaras_client.adapter.soap.options');
//        $this->assertEquals(2, $soapOptions['soap_version']);
//        $this->assertEquals(false, $soapOptions['wsdl_cache_enabled']);
//        $this->assertEquals(0, $soapOptions['wsdl_cache_ttl']);
//        $this->assertEquals(0, $soapOptions['cache_wsdl']);
//        $this->assertEquals(1, $soapOptions['exceptions']);
//
//        $this->assertNull($container->getParameter('hocaras_client.image.server'));
//        $this->assertEquals(
//            '/images/hotelId_pictureId_width_height.extension',
//            $container->getParameter('hocaras_client.image.url')
//        );
//    }
//
//    public function testMissingWsdl()
//    {
//        $this->setExpectedException(
//            'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
//            'The child node "wsdl" at path "hocaras_client.adapter.soap" must be configured.'
//        );
//        $config = $this->getConfigYml()[$this->root];
//
//        $this->extension->load([$config], $container = $this->getContainer());
//    }


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