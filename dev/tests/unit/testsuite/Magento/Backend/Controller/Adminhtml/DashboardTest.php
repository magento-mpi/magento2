<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml;

class DashboardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_response;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_request = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->_response = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
    }

    protected function tearDown()
    {
        $this->_request = null;
        $this->_response = null;
        $this->_objectManager = null;
    }

    public function testTunnelAction()
    {
        $fixture = uniqid();
        $this->_request->expects(
            $this->at(0)
        )->method(
            'getParam'
        )->with(
            'ga'
        )->will(
            $this->returnValue(urlencode(base64_encode(json_encode(array(1)))))
        );
        $this->_request->expects($this->at(1))->method('getParam')->with('h')->will($this->returnValue($fixture));
        $tunnelResponse = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $httpClient = $this->getMock(
            'Magento\HTTP\ZendClient',
            array('setUri', 'setParameterGet', 'setConfig', 'request', 'getHeaders')
        );
        /** @var $helper \Magento\Backend\Helper\Dashboard\Data|PHPUnit_Framework_MockObject_MockObject */
        $helper = $this->getMock(
            'Magento\Backend\Helper\Dashboard\Data',
            array('getChartDataHash'),
            array(),
            '',
            false,
            false
        );
        $helper->expects($this->any())->method('getChartDataHash')->will($this->returnValue($fixture));

        $this->_objectManager->expects(
            $this->at(0)
        )->method(
            'get'
        )->with(
            'Magento\Backend\Helper\Dashboard\Data'
        )->will(
            $this->returnValue($helper)
        );
        $this->_objectManager->expects(
            $this->at(1)
        )->method(
            'create'
        )->with(
            'Magento\HTTP\ZendClient'
        )->will(
            $this->returnValue($httpClient)
        );
        $httpClient->expects($this->once())->method('setUri')->will($this->returnValue($httpClient));
        $httpClient->expects($this->once())->method('setParameterGet')->will($this->returnValue($httpClient));
        $httpClient->expects($this->once())->method('setConfig')->will($this->returnValue($httpClient));
        $httpClient->expects($this->once())->method('request')->with('GET')->will($this->returnValue($tunnelResponse));
        $tunnelResponse->expects(
            $this->any()
        )->method(
            'getHeaders'
        )->will(
            $this->returnValue(array('Content-type' => 'test_header'))
        );
        $this->_response->expects($this->any())->method('setHeader')->will($this->returnValue($this->_response));
        $tunnelResponse->expects($this->any())->method('getBody')->will($this->returnValue('success_msg'));
        $this->_response->expects(
            $this->once()
        )->method(
            'setBody'
        )->with(
            'success_msg'
        )->will(
            $this->returnValue($this->_response)
        );
        $this->_response->expects($this->any())->method('getBody')->will($this->returnValue('success_msg'));
        $controller = $this->_factory($this->_request, $this->_response);
        $controller->tunnelAction();
        $this->assertEquals('success_msg', $controller->getResponse()->getBody());
    }

    public function testTunnelAction400()
    {
        $this->_response->expects(
            $this->once()
        )->method(
            'setBody'
        )->with(
            'Service unavailable: invalid request'
        )->will(
            $this->returnValue($this->_response)
        );
        $this->_response->expects($this->any())->method('setHeader')->will($this->returnValue($this->_response));
        $this->_response->expects(
            $this->once()
        )->method(
            'setHttpResponseCode'
        )->with(
            400
        )->will(
            $this->returnValue($this->_response)
        );
        $this->_response->expects($this->once())->method('getHttpResponseCode')->will($this->returnValue(400));
        $controller = $this->_factory($this->_request, $this->_response);
        $controller->tunnelAction();
        $this->assertEquals(400, $controller->getResponse()->getHttpResponseCode());
    }

    public function testTunnelAction503()
    {
        $fixture = uniqid();
        $this->_request->expects(
            $this->at(0)
        )->method(
            'getParam'
        )->with(
            'ga'
        )->will(
            $this->returnValue(urlencode(base64_encode(json_encode(array(1)))))
        );
        $this->_request->expects($this->at(1))->method('getParam')->with('h')->will($this->returnValue($fixture));
        /** @var $helper \Magento\Backend\Helper\Dashboard\Data|PHPUnit_Framework_MockObject_MockObject */
        $helper = $this->getMock(
            'Magento\Backend\Helper\Dashboard\Data',
            array('getChartDataHash'),
            array(),
            '',
            false,
            false
        );
        $helper->expects($this->any())->method('getChartDataHash')->will($this->returnValue($fixture));

        $this->_objectManager->expects(
            $this->at(0)
        )->method(
            'get'
        )->with(
            'Magento\Backend\Helper\Dashboard\Data'
        )->will(
            $this->returnValue($helper)
        );
        $exceptionMock = new \Exception();
        $this->_objectManager->expects(
            $this->at(1)
        )->method(
            'create'
        )->with(
            'Magento\HTTP\ZendClient'
        )->will(
            $this->throwException($exceptionMock)
        );
        $loggerMock = $this->getMock('Magento\Logger', array('logException'), array(), '', false);
        $loggerMock->expects($this->once())->method('logException')->with($exceptionMock);
        $this->_objectManager->expects(
            $this->at(2)
        )->method(
            'get'
        )->with(
            'Magento\Logger'
        )->will(
            $this->returnValue($loggerMock)
        );

        $this->_response->expects(
            $this->once()
        )->method(
            'setBody'
        )->with(
            'Service unavailable: see error log for details'
        )->will(
            $this->returnValue($this->_response)
        );
        $this->_response->expects($this->any())->method('setHeader')->will($this->returnValue($this->_response));
        $this->_response->expects(
            $this->once()
        )->method(
            'setHttpResponseCode'
        )->with(
            503
        )->will(
            $this->returnValue($this->_response)
        );
        $this->_response->expects($this->once())->method('getHttpResponseCode')->will($this->returnValue(503));
        $controller = $this->_factory($this->_request, $this->_response);
        $controller->tunnelAction();
        $this->assertEquals(503, $controller->getResponse()->getHttpResponseCode());
    }

    /**
     * Create the tested object
     *
     * @param Magento\App\Request\Http $request
     * @param \Magento\App\Response\Http|null $response
     * @return \Magento\Backend\Controller\Adminhtml\Dashboard|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _factory($request, $response = null)
    {
        if (!$response) {
            /** @var $response \Magento\App\ResponseInterface|PHPUnit_Framework_MockObject_MockObject */
            $response = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
            $response->headersSentThrowsException = false;
        }
        $rewriteFactory = $this->getMock('Magento\UrlRewrite\Model\UrlRewriteFactory', array('create'), array(), '', false);
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $varienFront = $helper->getObject('Magento\App\FrontController', array('rewriteFactory' => $rewriteFactory));

        $arguments = array(
            'request' => $request,
            'response' => $response,
            'objectManager' => $this->_objectManager,
            'frontController' => $varienFront
        );
        $context = $helper->getObject('Magento\Backend\App\Action\Context', $arguments);
        return new \Magento\Backend\Controller\Adminhtml\Dashboard($context);
    }
}
