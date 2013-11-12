<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\Action\Plugin;

class InstallTests extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\App\Action\Plugin\Install
     */
    protected $_plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invocationChainMock;

    protected function setUp()
    {
        $this->_appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->_responseFactory = $this->getMock('Magento\App\ResponseFactory', array(), array(), '', false);
        $this->_urlMock = $this->getMock('Magento\Core\Model\Url', array(), array(), '', false);
        $this->_invocationChainMock =
            $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->_plugin = new \Magento\Core\App\Action\Plugin\Install(
            $this->_appStateMock,
            $this->_responseFactory,
            $this->_urlMock);
    }

    public function testAroundDispatch()
    {
        $url = 'http://example.com';
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(false));
        $response = $this->getMock('Magento\App\Response\Http', array('setRedirect'), array(), '', false);
        $this->_responseFactory->expects($this->once())->method('create')->will($this->returnValue($response));
        $this->_urlMock->expects($this->once())->method('getUrl')->with('install')->will($this->returnValue($url));
        $response->expects($this->once())->method('setRedirect')->with($url)->will($this->returnValue($response));
        $this->_invocationChainMock->expects($this->never())->method('proceed');
        $this->assertEquals($response, $this->_plugin->aroundDispatch(array(), $this->_invocationChainMock));
    }

    public function testAroundDispatchWhenApplicationIsInstalled()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));

        $this->_invocationChainMock
            ->expects($this->once())
            ->method('proceed')
            ->with(array())
            ->will($this->returnValue('ExpectedValue'));
        $this->assertEquals('ExpectedValue', $this->_plugin->aroundDispatch(array(), $this->_invocationChainMock));
    }
}