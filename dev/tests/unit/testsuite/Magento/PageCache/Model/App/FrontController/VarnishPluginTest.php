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
     * @var VarnishPlugin
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
     * @var \Magento\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\App\FrontControllerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $frontControllerMock;

    /**
     * @var \Closure
     */
    protected $closure;

    /**
     * @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * MessageBox instance
     *
     * @var \Magento\App\PageCache\MessageBox|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $msgBoxMock;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->configMock = $this->getMock('Magento\PageCache\Model\Config', array(), array(), '', false);
        $this->versionMock = $this->getMock('Magento\App\PageCache\Version', array(), array(), '', false);
        $this->stateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->frontControllerMock = $this->getMock(
            'Magento\App\FrontControllerInterface',
            array(),
            array(),
            '',
            false
        );
        $this->requestMock = $this->getMock('Magento\App\RequestInterface', array(), array(), '', false);
        $this->responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->msgBoxMock = $this->getMock('Magento\App\PageCache\MessageBox', array('process'), array(), '', false);
        $response = $this->responseMock;
        $this->closure = function () use ($response) {
            return $response;
        };
        $this->plugin = new VarnishPlugin(
            $this->configMock,
            $this->versionMock,
            $this->msgBoxMock,
            $this->stateMock
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAroundDispatchReturnsCache($state)
    {
        $this->configMock
            ->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $this->configMock
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(\Magento\PageCache\Model\Config::VARNISH));
        $this->versionMock
            ->expects($this->once())
            ->method('process');
        $this->msgBoxMock
            ->expects($this->once())
            ->method('process');
        $this->stateMock->expects($this->any())
            ->method('getMode')
            ->will($this->returnValue($state));
        if ($state == \Magento\App\State::MODE_DEVELOPER) {
            $this->responseMock->expects($this->once())
                ->method('setHeader')
                ->with('X-Magento-Debug');
        } else {
            $this->responseMock->expects($this->never())
                ->method('setHeader');
        }
        $this->plugin->aroundDispatch($this->frontControllerMock, $this->closure, $this->requestMock);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAroundDispatchDisabled($state)
    {
        $this->configMock
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(null));
        $this->versionMock
            ->expects($this->never())
            ->method('process');
        $this->msgBoxMock
            ->expects($this->never())
            ->method('process');
        $this->stateMock->expects($this->any())
            ->method('getMode')
            ->will($this->returnValue($state));
        $this->responseMock->expects($this->never())
            ->method('setHeader');
        $this->plugin->aroundDispatch($this->frontControllerMock, $this->closure, $this->requestMock);
    }

    public function dataProvider()
    {
        return array(
            'developer_mode' => array(\Magento\App\State::MODE_DEVELOPER),
            'production' => array(\Magento\App\State::MODE_PRODUCTION),
        );
    }
}
