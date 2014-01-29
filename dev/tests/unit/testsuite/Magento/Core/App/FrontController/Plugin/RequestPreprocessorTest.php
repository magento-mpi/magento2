<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\FrontController\Plugin;

class RequestPreprocessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\App\FrontController\Plugin\RequestPreprocessor
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invocationChainMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    protected function setUp()
    {
        $this->_storeMock = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $this->_requestMock = $this->getMock('\Magento\App\Request\Http', array(), array(), '', false);
        $this->_invocationChainMock
            = $this->getMock('\Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->_storeManagerMock = $this->getMock('\Magento\Core\Model\StoreManager', array(), array(), '', false);
        $this->_appStateMock = $this->getMock('\Magento\App\State', array(), array(), '', false);
        $this->_urlMock = $this->getMock('\Magento\Url', array(), array(), '', false);
        $this->_storeConfigMock = $this->getMock('\Magento\Core\Model\Store\Config', array(), array(), '', false);

        $this->_model = new \Magento\Core\App\FrontController\Plugin\RequestPreprocessor(
            $this->_storeManagerMock,
            $this->_appStateMock,
            $this->_urlMock,
            $this->_storeConfigMock,
            $this->getMock('\Magento\App\ResponseFactory', array(), array(), '', false)
        );
    }

    public function testAroundDispatchIfNotInstalled()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(false));
        $this->_requestMock->expects($this->once())->method('setDispatched')->with(false);
        $this->_storeConfigMock->expects($this->never())->method('getConfig');
        $this->_invocationChainMock->expects($this->once())->method('proceed')->with(array($this->_requestMock));
        $this->_model->aroundDispatch(array($this->_requestMock), $this->_invocationChainMock);
    }

    public function testAroundDispatchIfInstalledAndRedirectCodeNotExist()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_requestMock->expects($this->once())->method('setDispatched')->with(false);
        $this->_storeConfigMock->expects($this->once())->method('getConfig')->with('web/url/redirect_to_base');
        $this->_invocationChainMock->expects($this->once())->method('proceed')->with(array($this->_requestMock));
        $this->_requestMock->expects($this->never())->method('getRequestUri');
        $this->_model->aroundDispatch(array($this->_requestMock), $this->_invocationChainMock);
    }

    public function testAroundDispatchIfInstalledAndRedirectCodeExist()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_requestMock->expects($this->once())->method('setDispatched')->with(false);
        $this->_storeConfigMock
            ->expects($this->once())->method('getConfig')
            ->with('web/url/redirect_to_base')->will($this->returnValue(302));
        $this->_storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_storeMock));
        $this->_storeMock->expects($this->once())->method('getBaseUrl');
        $this->_invocationChainMock->expects($this->once())->method('proceed')->with(array($this->_requestMock));
        $this->_requestMock->expects($this->never())->method('getRequestUri');
        $this->_model->aroundDispatch(array($this->_requestMock), $this->_invocationChainMock);
    }

    public function testAroundDispatchIfBaseUrlNotExists()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_requestMock->expects($this->once())->method('setDispatched')->with(false);
        $this->_storeConfigMock
            ->expects($this->once())->method('getConfig')
            ->with('web/url/redirect_to_base')->will($this->returnValue(302));
        $this->_storeManagerMock
            ->expects($this->any())->method('getStore')->will($this->returnValue($this->_storeMock));
        $this->_storeMock->expects($this->once())->method('getBaseUrl')->will($this->returnValue(false));
        $this->_requestMock->expects($this->never())->method('getRequestUri');
        $this->_invocationChainMock->expects($this->once())->method('proceed')->with(array($this->_requestMock));
        $this->_model->aroundDispatch(array($this->_requestMock), $this->_invocationChainMock);
    }
}
