<?php
/**
 * Test for Mage_Webapi_Controller_Soap_Handler.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Soap_HandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Soap_Handler */
    protected $_handler;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_apiConfigMock;

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
        $this->markTestIncomplete("Needs to be fixed after service layer implementation.");
        /** Prepare mocks for SUT constructor. */
        $this->_apiConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Config')
            ->setMethods(
                array(
                    'getServiceNameByOperation',
                    'validateVersionNumber',
                    'getControllerClassByOperationName',
                    'getMethodNameByOperation',
                    'identifyVersionSuffix',
                    'checkDeprecationPolicy'
                )
            )->disableOriginalConstructor()
            ->getMock();
        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->setMethods(array('__', 'prepareMethodParams'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_authenticationMock = $this->getMockBuilder('Mage_Webapi_Controller_Soap_Authentication')
            ->setMethods(array('authenticate'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_factoryMock = $this->getMockBuilder('Mage_Webapi_Controller_Action_Factory')
            ->setMethods(array('createActionController'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_authorizationMock = $this->getMockBuilder('Mage_Webapi_Model_Authorization')
            ->setMethods(array('checkServiceAcl'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Soap_Request')
            ->setMethods(array('getRequestedServices'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_errorProcessorMock = $this->getMockBuilder('Mage_Webapi_Controller_ErrorProcessor')
            ->setMethods(array('maskException'))
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_handler = new Mage_Webapi_Controller_Soap_Handler(
            $this->_apiConfigMock,
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
        unset($this->_apiConfigMock);
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

    public function testCallMethodNotFoundException()
    {
        /** Prepare mock for authenticate(). */
        $this->_authenticationMock->expects($this->once())->method('authenticate');
        /** Prepare mock for _getOperationVersion() method. */
        $this->_requestMock->expects($this->once())
            ->method('getRequestedServices')
            ->will($this->returnValue(array('serviceName' => 'v1')));
        /** Create the arguments map of returned values for getServiceNameByOperation() method. */
        $getServiceValueMap = array(
            array('operation', null, 'serviceName'),
            array('operation', 'v1', false)
        );
        $this->_apiConfigMock->expects($this->any())
            ->method('getServiceNameByOperation')
            ->will($this->returnValueMap($getServiceValueMap));
        $this->_apiConfigMock->expects($this->once())
            ->method('validateVersionNumber')
            ->with(1, 'serviceName');
        $this->_helperMock->expects($this->once())
            ->method('__')
            ->with('Method "%s" is not found.', 'operation')
            ->will($this->returnValue('Method "operation" is not found.'));
        $this->setExpectedException(
            'Mage_Webapi_Model_Soap_Fault',
            'Method "operation" is not found.'
        );
        /** Execute SUT. */
        $this->_prepareSoapRequest();
        $this->_handler->__call('operation', $this->_arguments);
    }

    public function testCallInvalidOperationVersionException()
    {
        $this->_authenticationMock->expects($this->once())->method('authenticate');
        /** Prepare mock for _getOperationVersion() method. */
        $this->_requestMock->expects($this->once())
            ->method('getRequestedServices')
            ->will($this->returnValue(array('serviceName' => 'v1')));
        $this->_apiConfigMock->expects($this->once())
            ->method('getServiceNameByOperation')
            ->will($this->returnValue(false));
        $this->_helperMock->expects($this->once())
            ->method('__')
            ->with('The version of "%s" operation cannot be identified.', 'operationName')
            ->will($this->returnValue('The version of "operationName" operation cannot be identified.'));
        $this->setExpectedException(
            'Mage_Webapi_Model_Soap_Fault',
            'The version of "operationName" operation cannot be identified.'
        );
        /** Execute SUT. */
        $this->_prepareSoapRequest();
        $this->_handler->__call('operationName', $this->_arguments);
    }

    public function testCall()
    {
        /** Prepare mock for SUT. */
        $this->_prepareSoapRequest();
        $method = 'Get';
        $service = 'serviceName';
        $operation = $service . $method;
        $this->_authenticationMock->expects($this->once())->method('authenticate');
        $this->_requestMock->expects($this->once())
            ->method('getRequestedServices')
            ->will($this->returnValue(array($service => 'v1')));
        $this->_apiConfigMock->expects($this->any())
            ->method('getServiceNameByOperation')
            ->will($this->returnValue($service));
        $this->_apiConfigMock->expects($this->once())
            ->method('validateVersionNumber')
            ->with(1, $service);
        $versionAfterFallback = 'V1';
        $action = $method . $versionAfterFallback;
        $this->_apiConfigMock->expects($this->once())
            ->method('getControllerClassByOperationName')
            ->with($operation)
            ->will($this->returnValue('Vendor_Module_Controller_Webapi_Resource'));
        $controllerMock = $this->getMockBuilder('Vendor_Module_Controller_Webapi_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array($action))
            ->getMock();
        $this->_factoryMock->expects($this->once())
            ->method('createActionController')
            ->with('Vendor_Module_Controller_Webapi_Resource', $this->_requestMock)
            ->will($this->returnValue($controllerMock));
        $this->_apiConfigMock->expects($this->once())
            ->method('getMethodNameByOperation')
            ->with($operation, '1')
            ->will($this->returnValue($method));
        $this->_authorizationMock->expects($this->once())
            ->method('checkServiceAcl')
            ->with($service, $method);
        $this->_apiConfigMock->expects($this->once())
            ->method('identifyVersionSuffix')
            ->with($operation, '1', $controllerMock)
            ->will($this->returnValue($versionAfterFallback));
        $this->_apiConfigMock->expects($this->once())
            ->method('checkDeprecationPolicy')
            ->with($service, $method, $versionAfterFallback);
        $arguments = reset($this->_arguments);
        $arguments = get_object_vars($arguments);
        $this->_helperMock->expects($this->once())
            ->method('prepareMethodParams')
            ->with('Vendor_Module_Controller_Webapi_Resource', $action, $arguments, $this->_apiConfigMock)
            ->will($this->returnValue($arguments));
        $expectedResult = array('foo' => 'bar');
        $controllerMock->expects($this->once())
            ->method($action)
            ->with($arguments['customerId'])
            ->will($this->returnValue($expectedResult));

        /** Execute SUT. */
        $this->assertEquals(
            (object)array(Mage_Webapi_Controller_Soap_Handler::RESULT_NODE_NAME => $expectedResult),
            $this->_handler->__call($operation, $this->_arguments)
        );
    }

    /**
     * Process security header and prepare request arguments.
     */
    protected function _prepareSoapRequest()
    {
        /** Process security header by __call() method. */
        $this->_handler->setRequestHeaders(array(Mage_Webapi_Controller_Soap_Security::HEADER_SECURITY));
        $usernameToken = new stdClass();
        // @codingStandardsIgnoreStart
        $usernameToken->UsernameToken = new stdClass();
        $usernameToken->UsernameToken->Username = 'username';
        $usernameToken->UsernameToken->Password = 'password';
        $usernameToken->UsernameToken->Nonce = 'nonce';
        $usernameToken->UsernameToken->Created = 'created';
        // @codingStandardsIgnoreEnd
        $this->_handler->__call(
            Mage_Webapi_Controller_Soap_Security::HEADER_SECURITY,
            array($usernameToken)
        );

        /** Override arguments for process action header. */
        $request = new stdClass();
        $request->customerId = 1;
        $this->_arguments = array($request);
    }
}
