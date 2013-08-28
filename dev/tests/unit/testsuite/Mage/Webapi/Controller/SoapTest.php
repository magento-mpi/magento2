<?php
/**
 * Test SOAP dispatcher class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_SoapTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Soap */
    protected $_dispatcher;

    /** @var Mage_Webapi_Model_Soap_Server */
    protected $_soapServerMock;

    /** @var Mage_Webapi_Model_Soap_AutoDiscover */
    protected $_autoDiscoverMock;

    /** @var Mage_Webapi_Controller_Soap_Request */
    protected $_requestMock;

    /** @var Mage_Webapi_Model_Soap_Fault */
    protected $_soapFaultMock;

    /** @var Mage_Webapi_Controller_Response */
    protected $_responseMock;

    /** @var Mage_Webapi_Controller_ErrorProcessor */
    protected $_errorProcessorMock;

    /** @var Mage_Core_Model_StoreManager */
    protected $_storeManagerMock;

    /** @var Mage_Core_Model_App_State */
    protected $_appStateMock;

    /**
     * Set up dispatcher object.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_soapServerMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Server')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_soapServerMock->expects($this->any())->method('setWSDL')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setEncoding')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setSoapVersion')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setClassmap')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setReturnResponse')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setObject')->will($this->returnSelf());
        $this->_autoDiscoverMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_AutoDiscover')
            ->disableOriginalConstructor()
            ->setMethods(array('handle'))
            ->getMock();
        $this->_requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Soap_Request')
            ->setMethods(array('getParam', 'getRequestedServices'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Mage_Webapi_Controller_Response')
            ->disableOriginalConstructor()
            ->setMethods(array('clearHeaders', 'setHeader', 'setBody', 'sendResponse', 'setHttpResponseCode'))
            ->getMock();
        $this->_soapFaultMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Fault')
            ->disableOriginalConstructor()
            ->setMethods(array('getSoapFaultMessage', 'getLanguage'))
            ->getMock();
        $this->_errorProcessorMock = $this->getMockBuilder('Mage_Webapi_Controller_ErrorProcessor')
            ->disableOriginalConstructor()
            ->setMethods(array('maskException'))
            ->getMock();
        $soapHandlerMock = $this->getMockBuilder('Mage_Webapi_Controller_Soap_Handler')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_storeManagerMock =  $this->getMockBuilder('Mage_Core_Model_StoreManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_appStateMock =  $this->getMockBuilder('Mage_Core_Model_App_State')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));


        $this->_dispatcher = new Mage_Webapi_Controller_Soap(
            $this->_requestMock,
            $this->_responseMock,
            $this->_autoDiscoverMock,
            $this->_soapServerMock,
            $this->_soapFaultMock,
            $this->_errorProcessorMock,
            $soapHandlerMock,
            $this->_storeManagerMock,
            $this->_appStateMock
        );
    }

    /**
     * Clean up dispatcher and its dependencies.
     */
    protected function tearDown()
    {
        unset($this->_dispatcher);
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_autoDiscoverMock);
        unset($this->_soapServerMock);
        unset($this->_soapFaultMock);
        unset($this->_errorProcessorMock);
        unset($this->_storeManagerMock);
        unset($this->_appStateMock);

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
        $expectedUrl = 'http://magento.host/soap?wsdl=1&services=testModule3ErrorV1';
        $this->_requestMock->expects($this->once())
            ->method('getRequestedServices')
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

        $expectedException = new Mage_Webapi_Exception('Test message', Mage_Webapi_Exception::HTTP_BAD_REQUEST);
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

        $expectedUrl = 'http://magento.host/soap';

        $this->_soapServerMock->expects($this->any())
            ->method('getEndpointUri')
            ->will($this->returnValue($expectedUrl));

        $expectedFault = '<?xml version="1.0" encoding="utf8"?><root>SOAP_FAULT</root>';
        $this->_soapFaultMock->expects($this->once())
            ->method('getLanguage')
            ->will($this->returnValue('en'));
        $this->_soapFaultMock->expects($this->once())
            ->method('getSoapFaultMessage')
            ->with(
                $expectedException->getMessage(),
                Mage_Webapi_Model_Soap_Fault::FAULT_CODE_SENDER,
                'en'
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
            ->with(Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL)
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
