<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\App\FrontController;

class VarnishPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\App\FrontController\VarnishPlugin
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
     * @var \Magento\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateMock;

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
        $this->stateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->plugin = new VarnishPlugin($this->configMock, $this->versionMock, $this->stateMock);

        $this->responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->invocationChainMock =
            $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAroundDispatchProcess($state)
    {
        $this->configMock
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(\Magento\PageCache\Model\Config::VARNISH));
        $this->versionMock
            ->expects($this->once())
            ->method('process');
        $this->invocationChainMock
            ->expects($this->once())
            ->method('proceed')
            ->will($this->returnValue($this->responseMock));
        $this->stateMock->expects($this->any())
            ->method('getMode')
            ->will($this->returnValue($state));
        if ($state == \Magento\App\State::MODE_DEVELOPER) {
            $this->responseMock->expects($this->once())
                ->method('setHeader')
                ->with('X-Magento-Cache-Control');
        } else {
            $this->responseMock->expects($this->never())
                ->method('setHeader');
        }
        $this->plugin->aroundDispatch(array(), $this->invocationChainMock);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAroundDispatchDisabled($state)
    {
        $this->configMock
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(null));
        $this->versionMock
            ->expects($this->never())
            ->method('process');
        $this->invocationChainMock
            ->expects($this->once())
            ->method('proceed')
            ->will($this->returnValue($this->responseMock));
        $this->stateMock->expects($this->any())
            ->method('getMode')
            ->will($this->returnValue($state));
        $this->responseMock->expects($this->never())
            ->method('setHeader');
        $this->plugin->aroundDispatch(array(), $this->invocationChainMock);
    }

    public function dataProvider()
    {
        return array(
            'developer_mode' => array(\Magento\App\State::MODE_DEVELOPER),
            'production' => array(\Magento\App\State::MODE_PRODUCTION),
        );
    }
}
