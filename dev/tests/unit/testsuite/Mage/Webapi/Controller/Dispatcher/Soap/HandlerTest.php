<?php
/**
 * Test for Mage_Webapi_Controller_Dispatcher_Soap_Handler.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Dispatcher_Soap_HandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Dispatcher_Soap_Handler */
    protected $_handler;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_serviceConfigMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_soapApiConfigMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_helperMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_authenticationMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_factoryMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_authorizationMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_requestMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_errorProcessorMock;

    /** @var array */
    protected $_arguments;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_serviceConfigMock = $this->getMockBuilder('Mage_Core_Service_Config')
            ->setMethods(
                array(
                    'getServiceClassByServiceName',
                    'checkDeprecationPolicy',
                )
            )->disableOriginalConstructor()
            ->getMock();
        $this->_soapApiConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Config_Soap')
            ->setMethods(
                array(
                    'getMethodNameByOperation',
                    'getServiceNameByOperation'
                )
            )->disableOriginalConstructor()
            ->getMock();
        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->setMethods(array('__', 'prepareMethodParams'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_authenticationMock = $this->getMockBuilder('Mage_Webapi_Controller_Dispatcher_Soap_Authentication')
            ->setMethods(array('authenticate'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_factoryMock = $this->getMockBuilder('Mage_Core_Service_Factory')
            ->setMethods(array('createServiceInstance'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_authorizationMock = $this->getMockBuilder('Mage_Webapi_Model_Authorization')
            ->setMethods(array('checkResourceAcl'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Request_Soap')
            ->setMethods(array('getRequestedResources'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_errorProcessorMock = $this->getMockBuilder('Mage_Webapi_Controller_Dispatcher_ErrorProcessor')
            ->setMethods(array('maskException'))
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_handler = new Mage_Webapi_Controller_Dispatcher_Soap_Handler(
            $this->_serviceConfigMock,
            $this->_soapApiConfigMock,
            $this->_helperMock,
            $this->_authenticationMock,
            $this->_factoryMock,
            $this->_authorizationMock,
            $this->_requestMock,
            $this->_errorProcessorMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_handler);
        unset($this->_helperMock);
        unset($this->_authenticationMock);
        unset($this->_factoryMock);
        unset($this->_authorizationMock);
        unset($this->_requestMock);
        unset($this->_errorProcessorMock);
        parent::tearDown();
    }

    public function testCallEmptyUsernameTokenException()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_handler->setRequestHeaders(array('invalidHeader'));
        $this->_helperMock->expects($this->once())->method('__')->will($this->returnArgument(0));
        $this->setExpectedException(
            'Mage_Webapi_Model_Soap_Fault',
            'WS-Security UsernameToken is not found in SOAP-request.'
        );
        /** Execute SUT. */
        $this->_handler->__call('operation', array());
    }

    /**
     * This test is checking any other exceptions but Mage_Webapi_Exception handling during __call().
     */
    public function testCallException()
    {
        /** Prepare mocks for SUT constructor. */
        $exceptionMessage = 'Exception message.';
        $this->setExpectedException(
            'Mage_Webapi_Model_Soap_Fault',
            $exceptionMessage
        );
        $exception = new Exception($exceptionMessage);
        $this->_errorProcessorMock->expects($this->once())
            ->method('maskException')
            ->with($exception)
            ->will($this->returnValue($exception));
        /** Model situation: authenticate() method throws Exception(). */
        $this->_authenticationMock->expects($this->once())
            ->method('authenticate')
            ->will($this->throwException($exception));
        /** Execute SUT. */
        $this->_prepareSoapRequest();
        $this->_handler->__call('operation', $this->_arguments);
    }

    public function testCall()
    {
        /** Prepare mock for SUT. */
        $this->_prepareSoapRequest();
        $method = 'Get';
        $serviceName = 'serviceName';
        $operation = $serviceName . $method;
        $this->_authenticationMock->expects($this->once())->method('authenticate');
        $this->_soapApiConfigMock->expects($this->any())
            ->method('getServiceNameByOperation')
            ->will($this->returnValue($serviceName));
        $serviceMock = $this->getMockBuilder('Vendor_Module_Controller_Webapi_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array($method))
            ->getMock();
        $this->_factoryMock->expects($this->once())
            ->method('createServiceInstance')
            ->with($serviceName)
            ->will($this->returnValue($serviceMock));
        $this->_soapApiConfigMock->expects($this->once())
            ->method('getMethodNameByOperation')
            ->with($operation)
            ->will($this->returnValue($method));
        // TODO: Uncomment when authorization is enabled
        //$this->_authorizationMock->expects($this->once())
        //   ->method('checkResourceAcl')
        //    ->with($serviceName, $method);
        $this->_serviceConfigMock->expects($this->once())
            ->method('checkDeprecationPolicy')
            ->with($serviceName, $method);
        $arguments = reset($this->_arguments);
        $arguments = get_object_vars($arguments);
        $this->_helperMock->expects($this->once())
            ->method('prepareMethodParams')
            ->with($serviceName, $method, $arguments, $this->_serviceConfigMock)
            ->will($this->returnValue($arguments));
        $expectedResult = array('foo' => 'bar');
        $serviceMock->expects($this->once())
            ->method($method)
            ->with($arguments['customerId'])
            ->will($this->returnValue($expectedResult));

        /** Execute SUT. */
        $this->assertEquals(
            (object)array(Mage_Webapi_Controller_Dispatcher_Soap_Handler::RESULT_NODE_NAME => $expectedResult),
            $this->_handler->__call($operation, $this->_arguments)
        );
    }

    /**
     * Process security header and prepare request arguments.
     */
    protected function _prepareSoapRequest()
    {
        /** Process security header by __call() method. */
        $this->_handler->setRequestHeaders(array(Mage_Webapi_Controller_Dispatcher_Soap_Handler::HEADER_SECURITY));
        $usernameToken = new stdClass();
        // @codingStandardsIgnoreStart
        $usernameToken->UsernameToken = new stdClass();
        $usernameToken->UsernameToken->Username = 'username';
        $usernameToken->UsernameToken->Password = 'password';
        $usernameToken->UsernameToken->Nonce = 'nonce';
        $usernameToken->UsernameToken->Created = 'created';
        // @codingStandardsIgnoreEnd
        $this->_handler->__call(
            Mage_Webapi_Controller_Dispatcher_Soap_Handler::HEADER_SECURITY,
            array($usernameToken)
        );

        /** Override arguments for process action header. */
        $request = new stdClass();
        $request->customerId = 1;
        $this->_arguments = array($request);
    }
}
