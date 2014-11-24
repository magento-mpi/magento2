<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller;

class NorouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Controller\Noroute
     */
    protected $_controller;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cmsHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $responseMock = $this->getMock('Magento\Framework\App\Response\Http', array(), array(), '', false);
        $responseMock->expects(
            $this->at(0)
        )->method(
            'setHeader'
        )->with(
            'HTTP/1.1',
            '404 Not Found'
        )->will(
            $this->returnValue($responseMock)
        );
        $responseMock->expects(
            $this->at(1)
        )->method(
            'setHeader'
        )->with(
            'Status',
            '404 File not found'
        )->will(
            $this->returnValue($responseMock)
        );

        $scopeConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_requestMock = $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false);
        $this->_cmsHelperMock = $this->getMock('Magento\Cms\Helper\Page', array(), array(), '', false);
        $valueMap = array(
            array(
                'Magento\Framework\App\Config\ScopeConfigInterface',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $scopeConfigMock
            ),
            array('Magento\Cms\Helper\Page', $this->_cmsHelperMock)
        );
        $objectManagerMock->expects($this->any())->method('get')->will($this->returnValueMap($valueMap));
        $scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            \Magento\Cms\Helper\Page::XML_PATH_NO_ROUTE_PAGE
        )->will(
            $this->returnValue('pageId')
        );
        $this->_controller = $helper->getObject(
            'Magento\Cms\Controller\Noroute\Index',
            array('response' => $responseMock, 'objectManager' => $objectManagerMock, 'request' => $this->_requestMock)
        );
    }

    /**
     * @param bool $renderPage
     * @dataProvider indexActionDataProvider
     */
    public function testIndexAction($renderPage)
    {
        $this->_cmsHelperMock->expects($this->once())->method('renderPage')->will($this->returnValue($renderPage));
        $this->_requestMock->expects($this->any())->method('setActionName')->with('defaultNoRoute');
        $this->_controller->execute();
    }

    public function indexActionDataProvider()
    {
        return array('renderPage_return_true' => array(true), 'renderPage_return_false' => array(false));
    }
}
