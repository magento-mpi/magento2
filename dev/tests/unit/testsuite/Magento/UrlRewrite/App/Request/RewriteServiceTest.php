<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\App\Request;

class RewriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\UrlRewrite\App\Request\RewriteService
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_routerListMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_rewriteFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {
        $this->_routerListMock = $this->getMock('\Magento\App\RouterList', array(), array(), '', false);
        $this->_configMock = $this->getMock('\Magento\App\Config\ScopeConfigInterface');
        $this->_requestMock = $this->getMock('\Magento\App\Request\Http', array(), array(), '', false);
        $this->_rewriteFactoryMock = $this->getMock(
            '\Magento\UrlRewrite\Model\UrlRewriteFactory',
            array('create'),
            array(),
            '',
            false
        );

        $this->_model = new \Magento\UrlRewrite\App\Request\RewriteService(
            $this->_routerListMock,
            $this->_rewriteFactoryMock,
            $this->_configMock
        );
    }

    public function testApplyRewritesWhenRequestIsStraight()
    {
        $this->_requestMock->expects($this->once())->method('isStraight')->will($this->returnValue(true));
        $this->_rewriteFactoryMock->expects($this->never())->method('create')->will($this->returnValue('nodeName'));
        $this->_model->applyRewrites($this->_requestMock);
    }

    public function testApplyRewritesWhenRequestIsNotStraight()
    {
        $this->_requestMock->expects($this->once())->method('isStraight')->will($this->returnValue(false));
        $urlRewriteMock = $this->getMock('\Magento\UrlRewrite\Model\UrlRewrite', array(), array(), '', false);
        $this->_rewriteFactoryMock->expects(
            $this->once()
        )->method(
            'create'
        )->will(
            $this->returnValue($urlRewriteMock)
        );
        $this->_model->applyRewrites($this->_requestMock);
    }
}
