<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\App\FrontController;

class BuiltinPluginTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Closure */
    protected $closure;

    /**
     * @var \Magento\PageCache\Model\App\FrontController\BuiltinPlugin
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
     * @var \Magento\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateMock;

    /**
     * @var \Magento\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\App\FrontControllerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $frontControllerMock;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->configMock = $this->getMock('Magento\PageCache\Model\Config', array(), array(), '', false);
        $this->versionMock = $this->getMock('Magento\App\PageCache\Version', array(), array(), '', false);
        $this->kernelMock = $this->getMock('Magento\App\PageCache\Kernel', array(), array(), '', false);
        $this->stateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->frontControllerMock = $this->getMock('Magento\App\FrontController', array(), array(), '', false);
        $this->plugin = new BuiltinPlugin($this->configMock, $this->versionMock, $this->kernelMock, $this->stateMock);

        $this->responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $response = $this->responseMock;
        $this->closure = function () use ($response) {
            return $response;
        };
        $this->requestMock = $this->getMockForAbstractClass('Magento\App\RequestInterface');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAroundDispatchProcessIfCacheMissed($state)
    {
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $this->configMock
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(\Magento\PageCache\Model\Config::BUILT_IN));
        $this->versionMock
            ->expects($this->once())
            ->method('process');
        $this->kernelMock
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue(false));
        $this->stateMock->expects($this->any())
            ->method('getMode')
            ->will($this->returnValue($state));
        if ($state == \Magento\App\State::MODE_DEVELOPER) {
            $this->responseMock->expects($this->at(1))
                ->method('setHeader')
                ->with('X-Magento-Cache-Control');
            $this->responseMock->expects($this->at(2))
                ->method('setHeader')
                ->with('X-Magento-Cache-Debug');
        } else {
            $this->responseMock->expects($this->never())
                ->method('setHeader');
        }
        $this->kernelMock
            ->expects($this->once())
            ->method('process')
            ->with($this->responseMock);
        $this->plugin->aroundDispatch($this->frontControllerMock, $this->closure, $this->requestMock);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAroundDispatchReturnsCache($state)
    {
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $this->configMock
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(\Magento\PageCache\Model\Config::BUILT_IN));
        $this->versionMock
            ->expects($this->once())
            ->method('process');
        $this->kernelMock
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue($this->responseMock));

        $this->stateMock->expects($this->any())
            ->method('getMode')
            ->will($this->returnValue($state));
        if ($state == \Magento\App\State::MODE_DEVELOPER) {
            $this->responseMock->expects($this->once())
                ->method('setHeader')
                ->with('X-Magento-Cache-Debug');
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
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));
        $this->configMock
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(null));
        $this->stateMock->expects($this->any())
            ->method('getMode')
            ->will($this->returnValue($state));
        $this->responseMock->expects($this->never())
            ->method('setHeader');
        $this->plugin->aroundDispatch($this->frontControllerMock,$this->closure, $this->requestMock);
    }

    public function dataProvider()
    {
        return array(
            'developer_mode' => array(\Magento\App\State::MODE_DEVELOPER),
            'production' => array(\Magento\App\State::MODE_PRODUCTION),
        );
    }
}
