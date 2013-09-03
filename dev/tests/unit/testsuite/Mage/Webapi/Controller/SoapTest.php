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

    /** @var Mage_Webapi_Model_Soap_Wsdl_Generator */
    protected $_wsdlGeneratorMock;

    /** @var Mage_Webapi_Controller_Soap_Request */
    protected $_requestMock;

    /** @var Mage_Webapi_Model_Soap_Fault */
    protected $_soapFaultMock;

    /** @var Mage_Webapi_Controller_Response */
    protected $_responseMock;

    /** @var Mage_Webapi_Controller_ErrorProcessor */
    protected $_errorProcessorMock;

    /** @var Mage_Webapi_Controller_Soap_Handler */
    protected $_soapHandlerMock;

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
        $this->_wsdlGeneratorMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Wsdl_Generator')
            ->disableOriginalConstructor()
            ->setMethods(array('generate'))
            ->getMock();
        $this->_requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Soap_Request')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Mage_Webapi_Controller_Response')
            ->disableOriginalConstructor()
            ->setMethods(array('clearHeaders', 'setHeader', 'sendResponse'))
            ->getMock();
        $this->_soapFaultMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Fault')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_errorProcessorMock = $this->getMockBuilder('Mage_Webapi_Controller_ErrorProcessor')
            ->disableOriginalConstructor()
            ->setMethods(array('maskException'))
            ->getMock();
        $this->_soapHandlerMock = $this->getMockBuilder('Mage_Webapi_Controller_Soap_Handler')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_appStateMock =  $this->getMockBuilder('Mage_Core_Model_App_State')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_responseMock->expects($this->any())->method('clearHeaders')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setWSDL')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setEncoding')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setReturnResponse')->will($this->returnSelf());

        $this->_dispatcher = new Mage_Webapi_Controller_Soap(
            $this->_requestMock,
            $this->_responseMock,
            $this->_wsdlGeneratorMock,
            $this->_soapServerMock,
            $this->_soapFaultMock,
            $this->_errorProcessorMock,
            $this->_soapHandlerMock,
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
        unset($this->_wsdlGeneratorMock);
        unset($this->_soapServerMock);
        unset($this->_soapFaultMock);
        unset($this->_errorProcessorMock);
        unset($this->_soapHandlerMock);
        unset($this->_appStateMock);

        parent::tearDown();
    }


    /**
     * Test redirected to install page
     */
    public function testRedirectToInstallPage()
    {
        $this->_appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(false));
        $this->_soapFaultMock->expects($this->any())
            ->method('getSoapFaultMessage')
            ->will($this->returnArgument(0));

        $this->_dispatcher->dispatch();
        $this->assertEquals('Magento is not yet installed', $this->_responseMock->getBody());
    }

    /**
     * Test successful WSDL content generation.
     */
    public function testDispatchWsdl()
    {
        $this->_appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $this->_mockGetParam(Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL, 1);
        $wsdl = 'Some WSDL content';
        $this->_wsdlGeneratorMock->expects($this->any())
            ->method('generate')
            ->will($this->returnValue($wsdl));

        $this->_dispatcher->dispatch();
        $this->assertEquals($wsdl, $this->_responseMock->getBody());
    }

    /**
     * Test successful SOAP action request dispatch.
     */
    public function testDispatchSoapRequest()
    {
        $this->_appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $soapResponse = 'Some some response';
        $this->_soapServerMock->expects($this->any())
            ->method('handle')
            ->will($this->returnValue($soapResponse));

        $this->_dispatcher->dispatch();
        $this->assertEquals($soapResponse, $this->_responseMock->getBody());
    }

    /**
     * Test handling exception during dispatch.
     */
    public function testDispatchWithException()
    {
        $this->_appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $exceptionMessage = 'some error message';
        $exception = new Mage_Webapi_Exception($exceptionMessage, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        $this->_soapServerMock->expects($this->any())
            ->method('handle')
            ->will($this->throwException($exception));
        $this->_errorProcessorMock->expects($this->any())
            ->method('maskException')
            ->will($this->returnValue($exception));
        $this->_soapFaultMock->expects($this->any())
            ->method('getSoapFaultMessage')
            ->will($this->returnArgument(0));

        $this->_dispatcher->dispatch();
        $this->assertEquals($exceptionMessage, $this->_responseMock->getBody());
    }

    /**
     * Mock getParam() of request object to return given value.
     *
     * @param $value
     */
    protected function _mockGetParam($param, $value)
    {
        $this->_requestMock->expects($this->once())
            ->method('getParam')
            ->with($param)
            ->will($this->returnValue($value));
    }

}
