<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\App\FrontController;

class CachePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\App\FrontController\CachePlugin
     */
    protected $plugin;

    /**
     * @var \Magento\PageCache\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\App\PageCache\Version|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $versionMock;

    /**
     * @var \Magento\App\PageCache\Kernel|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $kernelMock;

    /**
     * @var \Magento\Code\Plugin\InvocationChain|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invocationChainMock;

    /**
     * @var \Magento\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->configMock = $this->getMock('Magento\PageCache\Model\Config', array(), array(), '', false);
        $this->versionMock = $this->getMock('Magento\App\PageCache\Version', array(), array(), '', false);
        $this->kernelMock = $this->getMock('Magento\App\PageCache\Kernel', array(), array(), '', false);
        $this->plugin = new CachePlugin($this->configMock, $this->versionMock, $this->kernelMock);

        $this->responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->invocationChainMock =
            $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
    }

    public function testAroundDispatchProcessIfCacheMissedForBuiltIn()
    {
        $this->versionMock
            ->expects($this->once())
            ->method('process');
        $this->configMock
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(\Magento\PageCache\Model\Config::BUILT_IN));
        $this->kernelMock
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue(false));
        $this->invocationChainMock
            ->expects($this->once())
            ->method('proceed')
            ->will($this->returnValue($this->responseMock));
        $this->kernelMock
            ->expects($this->once())
            ->method('process')
            ->with($this->responseMock);
        $this->plugin->aroundDispatch(array(), $this->invocationChainMock);
    }

    public function testAroundDispatchReturnsCacheForBuiltIn()
    {
        $this->versionMock
            ->expects($this->once())
            ->method('process');
        $this->configMock
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(\Magento\PageCache\Model\Config::BUILT_IN));
        $this->kernelMock
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue($this->responseMock));
        $this->invocationChainMock
            ->expects($this->never())
            ->method('proceed');
        $this->plugin->aroundDispatch(array(), $this->invocationChainMock);
    }

    public function testAroundDispatchVarnish()
    {
        $this->versionMock
            ->expects($this->once())
            ->method('process');
        $this->configMock
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(\Magento\PageCache\Model\Config::VARNISH));
        $this->invocationChainMock
            ->expects($this->once())
            ->method('proceed');
        $this->plugin->aroundDispatch(array(), $this->invocationChainMock);
    }
}
