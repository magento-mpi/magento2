<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Interception\ObjectManager;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Interception\ObjectManager\Config
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $interceptionConfig;

    protected function setUp()
    {
        $this->interceptionConfig = $this->getMock('\Magento\Interception\Config');
        $this->model = new Config();
    }

    public function testGetInstanceTypeReturnsInterceptorClass()
    {
        $this->interceptionConfig->expects($this->once())->method('hasPlugins')->will($this->returnValue(true));
        $this->model->setInterceptionConfig($this->interceptionConfig);

        $this->assertEquals('SomeClass\Interceptor', $this->model->getInstanceType('SomeClass'));
    }

    public function testGetInstanceTypeReturnsSimpleClassIfNoPluginsAreDeclared()
    {
        $this->model->setInterceptionConfig($this->interceptionConfig);

        $this->assertEquals('SomeClass', $this->model->getInstanceType('SomeClass'));
    }

    public function testGetInstanceTypeReturnsSimpleClassIfInterceptionConfigIsNotSet()
    {
        $this->assertEquals('SomeClass', $this->model->getInstanceType('SomeClass'));
    }

    public function testGetOriginalInstanceTypeReturnsInterceptedClass()
    {
        $this->interceptionConfig->expects($this->once())->method('hasPlugins')->will($this->returnValue(true));
        $this->model->setInterceptionConfig($this->interceptionConfig);

        $this->assertEquals('SomeClass\Interceptor', $this->model->getInstanceType('SomeClass'));
        $this->assertEquals('SomeClass', $this->model->getOriginalInstanceType('SomeClass'));
    }
}
