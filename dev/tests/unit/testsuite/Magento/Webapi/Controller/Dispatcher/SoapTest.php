<?php
/**
 * Test SOAP dispatcher class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Dispatcher_SoapTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Dispatcher\Soap */
    protected $_dispatcher;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_apiConfigMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_soapServerMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_autoDiscoverMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_requestMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_soapFaultMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_responseMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_errorProcessorMock;

    /**
     * Set up dispatcher object.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_apiConfigMock = $this->getMockBuilder('Magento\Webapi\Model\Config\Soap')
            ->disableOriginalConstructor()
            ->setMethods(array('getAllResourcesVersions'))
            ->getMock();
        $this->_soapServerMock = $this->getMockBuilder('Magento\Webapi\Model\Soap\Server')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_soapServerMock->expects($this->any())->method('setWSDL')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setEncoding')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setSoapVersion')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setClassmap')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setReturnResponse')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setObject')->will($this->returnSelf());
        $this->_autoDiscoverMock = $this->getMockBuilder('Magento\Webapi\Model\Soap\AutoDiscover')
            ->disableOriginalConstructor()
            ->setMethods(array('handle'))
            ->getMock();
        $this->_requestMock = $this->getMockBuilder('Magento\Webapi\Controller\Request\Soap')
            ->setMethods(array('getParam', 'getRequestedResources'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Magento\Webapi\Controller\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('clearHeaders', 'setHeader', 'setBody', 'sendResponse', 'setHttpResponseCode'))
            ->getMock();
        $this->_soapFaultMock = $this->getMockBuilder('Magento\Webapi\Model\Soap\Fault')
            ->disableOriginalConstructor()
            ->setMethods(array('getSoapFaultMessage'))
            ->getMock();
        $this->_errorProcessorMock = $this->getMockBuilder('Magento\Webapi\Controller\Dispatcher\ErrorProcessor')
            ->disableOriginalConstructor()
            ->setMethods(array('maskException'))
            ->getMock();
        $soapHandlerMock = $this->getMockBuilder('Magento\Webapi\Controller\Dispatcher\Soap\Handler')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_dispatcher = new \Magento\Webapi\Controller\Dispatcher\Soap(
            $this->_apiConfigMock,
            $this->_requestMock,
            $this->_responseMock,
            $this->_autoDiscoverMock,
            $this->_soapServerMock,
            $this->_soapFaultMock,
            $this->_errorProcessorMock,
            $soapHandlerMock
        );
    }

    /**
     * Clean up dispatcher and its dependencies.
     */
    protected function tearDown()
    {
        unset($this->_dispatcher);
        unset($this->_apiConfigMock);
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_autoDiscoverMock);
        unset($this->_soapServerMock);
        unset($this->_soapFaultMock);
        unset($this->_errorProcessorMock);

        parent::tearDown();
    }

    /**
     * Test successful WSDL content generation.
     */
    public function testDispatchWsdl()
    {
        $this->_mockGetParam(1);

        $charset = 'utf8';
        $contentType = 'text/xml';
        $this->_mockSetResponseContentType($contentType, $charset);

        $expectedWsdl = '<?xml version="1.0" encoding="' . $charset .'"?><root>WSDL_CONTENT</root>';
        $expectedResources = array('foo' => 'v1');
        $expectedUrl = 'http://magento.host/api/soap?resources[foo]=v1';
        $this->_requestMock->expects($this->once())
            ->method('getRequestedResources')
            ->will($this->returnValue($expectedResources));
        $this->_soapServerMock->expects($this->once())
            ->method('generateUri')
            ->will($this->returnValue($expectedUrl));
        $this->_autoDiscoverMock->expects($this->once())
            ->method('handle')
            ->with($expectedResources, $expectedUrl)
            ->will($this->returnValue($expectedWsdl));

        $this->_mockSetResponse($expectedWsdl);

        $this->_dispatcher->dispatch();
    }

    /**
     * Test successful SOAP action request dispatch.
     */
    public function testDispatchSoapRequest()
    {
        $this->_mockGetParam(null);

        $charset = 'utf8';
        $contentType = 'application/soap+xml';
        $this->_mockSetResponseContentType($contentType, $charset);
        $expectedResponse = '<?xml version="1.0" encoding="' . $charset .'"?><root>SOAP_RESPONSE</root>';

        $this->_soapServerMock->expects($this->once())
            ->method('initWsdlCache')
            ->with();
        $this->_soapServerMock->expects($this->once())
            ->method('handle')
            ->will($this->returnValue($expectedResponse));

        $this->_mockSetResponse($expectedResponse);

        $this->_dispatcher->dispatch();
    }

    /**
     * Test handling exception during dispatch.
     */
    public function testDispatchWithException()
    {
        $this->_mockGetParam(null);

        $expectedException = new \Magento\Webapi\Exception('Test message', \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
        $this->_soapServerMock->expects($this->once())
            ->method('handle')
            ->will($this->throwException($expectedException));

        $this->_mockSetResponseContentType('text/xml');
        $this->_errorProcessorMock->expects($this->once())
            ->method('maskException')
            ->with($expectedException)
            ->will($this->returnArgument(0));
        $this->_responseMock->expects($this->once())
            ->method('setHttpResponseCode')
            ->with(400);

        $expectedResources = array(
            'foo' => array('v1'),
            'bar' => array('v2'),
        );
        $expectedUrl = 'http://magento.host/api/soap/';
        $this->_apiConfigMock->expects($this->once())
            ->method('getAllResourcesVersions')
            ->will($this->returnValue($expectedResources));
        $this->_soapServerMock->expects($this->any())
            ->method('getEndpointUri')
            ->will($this->returnValue($expectedUrl));
        $expectedDetails = array(
            'availableResources' => array(
                'foo' => array('v1' => $expectedUrl . '?wsdl&resources[foo]=v1'),
                'bar' => array('v2' => $expectedUrl . '?wsdl&resources[bar]=v2'),
            )
        );
        $expectedFault = '<?xml version="1.0" encoding="utf8"?><root>SOAP_FAULT</root>';
        $this->_soapFaultMock->expects($this->once())
            ->method('getSoapFaultMessage')
            ->with(
                $expectedException->getMessage(),
                \Magento\Webapi\Model\Soap\Fault::FAULT_CODE_SENDER,
                'en',
                $expectedDetails
            )
            ->will($this->returnValue($expectedFault));

        $this->_dispatcher->dispatch();
    }

    /**
     * Mock getParam() of request object to return given value.
     *
     * @param $value
     */
    protected function _mockGetParam($value)
    {
        $this->_requestMock->expects($this->once())
            ->method('getParam')
            ->with(\Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_WSDL)
            ->will($this->returnValue($value));
    }

    /**
     * Mock dispatcher _setResponseContentType() method.
     *
     * @param $charset
     * @param $contentType
     */
    protected function _mockSetResponseContentType($contentType = 'application/soap+xml', $charset = 'utf8')
    {
        $this->_responseMock->expects($this->once())
            ->method('clearHeaders')
            ->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())
            ->method('getApiCharset')
            ->will($this->returnValue($charset));
        $this->_responseMock->expects($this->once())
            ->method('setHeader')
            ->with('Content-Type', "{$contentType}; charset={$charset}");
    }

    /**
     * Mock setting response object to expect given response body.
     *
     * @param $expectedResponse
     */
    protected function _mockSetResponse($expectedResponse)
    {
        $this->_responseMock->expects($this->once())
            ->method('setBody')
            ->with($expectedResponse);
        $this->_responseMock->expects($this->once())
            ->method('sendResponse')
            ->with();
    }
}
