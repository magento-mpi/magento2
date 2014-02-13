<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Interception;

class FactoryDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    /**
     * @var FactoryDecorator
     */
    private $decorator;

    protected function setUp()
    {
        $this->factory = $this->getMockForAbstractClass('\Magento\ObjectManager\Factory');
        $this->config = $this->getMockForAbstractClass('\Magento\Interception\Config');
        $pluginList = $this->getMockForAbstractClass('\Magento\Interception\PluginList');
        $objectManager = $this->getMockForAbstractClass('\Magento\ObjectManager');
        $this->decorator = new FactoryDecorator($this->factory, $this->config, $pluginList, $objectManager);
    }

    public function testCreateDecorated()
    {
        $this->config->expects($this->once())->method('hasPlugins')->with('type')->will($this->returnValue(true));
        $this->config
            ->expects($this->once())
            ->method('getInterceptorClassName')
            ->with('type')
            ->will($this->returnValue('StdClass'))
        ;
        $this->assertInstanceOf('StdClass', $this->decorator->create('type'));
    }

    public function testCreateClean()
    {
        $this->config->expects($this->once())->method('hasPlugins')->with('type')->will($this->returnValue(false));
        $this->config->expects($this->never())->method('getInterceptorClassName');
        $this->factory
            ->expects($this->once())
            ->method('create')
            ->with('type', array(1, 2, 3))
            ->will($this->returnValue('test'))
        ;
        $this->assertEquals('test', $this->decorator->create('type', array(1, 2, 3)));
    }
} 
