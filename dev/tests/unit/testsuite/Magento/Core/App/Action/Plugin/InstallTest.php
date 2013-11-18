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
    protected $_responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invocationChainMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_actionFlagMock;

    protected function setUp()
    {
        $this->_appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->_responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->_urlMock = $this->getMock('Magento\Core\Model\Url', array(), array(), '', false);
        $this->_invocationChainMock =
            $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->_actionFlagMock = $this->getMock('Magento\App\ActionFlag', array(), array(), '', false);
        $this->_plugin = new \Magento\Core\App\Action\Plugin\Install(
            $this->_appStateMock,
            $this->_responseMock,
            $this->_urlMock,
            $this->_actionFlagMock
        );
    }

    public function testAroundDispatch()
    {
        $url = 'http://example.com';
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(false));
        $this->_actionFlagMock
            ->expects($this->once())
            ->method('set')->with('', \Magento\App\Action\Action::FLAG_NO_DISPATCH, true);
        $this->_urlMock->expects($this->once())->method('getUrl')->with('install')->will($this->returnValue($url));
        $this->_responseMock->expects($this->once())
            ->method('setRedirect')
            ->with($url)->will($this->returnValue($this->_responseMock));
        $this->_invocationChainMock->expects($this->never())->method('proceed');
        $this->_plugin->aroundDispatch(array(), $this->_invocationChainMock);
    }

    public function testAroundDispatchWhenApplicationIsInstalled()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));

        $this->_invocationChainMock
            ->expects($this->once())
            ->method('proceed')
            ->with(array())
            ->will($this->returnValue('ExpectedValue'));
        $this->_plugin->aroundDispatch(array(), $this->_invocationChainMock);
    }
}