<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\App\Action\Plugin;

class InstallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Install\App\Action\Plugin\Install
     */
    protected $_plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    protected function setUp()
    {
        $this->_appStateMock = $this->getMock('Magento\Framework\App\State', array(), array(), '', false);
        $this->_response =
            $this->getMock('Magento\Framework\App\ResponseInterface', array('setRedirect', 'sendResponse'));
        $this->_urlMock = $this->getMock('Magento\Url', array(), array(), '', false);
        $this->closureMock = function () {
            return 'ExpectedValue';
        };
        $this->subjectMock = $this->getMock('Magento\Framework\App\Action\Action', array(), array(), '', false);
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->_plugin = new \Magento\Install\App\Action\Plugin\Install(
            $this->_appStateMock,
            $this->_response,
            $this->_urlMock,
            $this->getMock('Magento\Framework\App\ActionFlag', array(), array(), '', false)
        );
    }

    public function testAroundDispatchWhenApplicationIsNotInstalled()
    {
        $url = 'http://example.com';
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(false));
        $this->_urlMock->expects($this->once())->method('getUrl')->with('install')->will($this->returnValue($url));
        $this->_response->expects($this->once())->method('setRedirect')->with($url);
        $this->_plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock);
    }

    public function testAroundDispatchWhenApplicationIsInstalled()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));

        $this->assertEquals(
            'ExpectedValue',
            $this->_plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }
}
