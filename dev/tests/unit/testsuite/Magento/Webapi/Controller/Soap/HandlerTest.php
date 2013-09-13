<?php
/**
 * Test for Magento_Webapi_Controller_Soap_Handler.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Soap_HandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Controller_Soap_Handler */
    protected $_handler;

    /** @var Magento_Core_Model_App */
    protected $_appMock;

    /** @var Magento_ObjectManager */
    protected $_objectManagerMock;

    /** @var Magento_Webapi_Controller_Soap_Security */
    protected $_soapSecurityMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_apiConfigMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_requestMock;

    /** @var array */
    protected $_arguments;

    protected function setUp()
    {
        $this->markTestIncomplete("Needs to be fixed after service layer implementation.");
        /** Prepare mocks for SUT constructor. */
        $this->_apiConfigMock = $this->getMockBuilder('Magento_Webapi_Model_Soap_Config')
            ->setMethods(
                array(
                    'getServiceNameByOperation',
                    'getControllerClassByOperationName',
                    'getMethodNameByOperation',
                )
            )->disableOriginalConstructor()
            ->getMock();
        $this->_requestMock = $this->getMockBuilder('Magento_Webapi_Controller_Soap_Request')
            ->setMethods(array('getRequestedServices'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_appMock = $this->getMockBuilder('Magento_Core_Model_App')->disableOriginalConstructor()->getMock();
        $this->_objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')->disableOriginalConstructor()
            ->getMock();

        $this->_soapSecurityMock = $this->getMockBuilder('Magento_Webapi_Controller_Soap_Security')
            ->disableOriginalConstructor()->getMock();
        /** Initialize SUT. */
        $this->_handler = new Magento_Webapi_Controller_Soap_Handler(
            $this->_appMock,
            $this->_requestMock,
            $this->_objectManagerMock,
            $this->_apiConfigMock,
            $this->_soapSecurityMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_handler);
        unset($this->_apiConfigMock);
        unset($this->_requestMock);
        parent::tearDown();
    }

    public function testCallEmptyUsernameTokenException()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_handler->setRequestHeaders(array('invalidHeader'));
        $this->setExpectedException(
            'Magento_Webapi_Model_Soap_Fault',
            'WS-Security UsernameToken is not found in SOAP-request.'
        );
        /** Execute SUT. */
        $this->_handler->__call('operation', array());
    }

    public function testCallMethodNotFoundException()
    {
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
        $this->setExpectedException(
            'Magento_Webapi_Model_Soap_Fault',
            'Method "operation" is not found.'
        );
        /** Execute SUT. */
        $this->_prepareSoapRequest();
        $this->_handler->__call('operation', $this->_arguments);
    }

    public function testCallInvalidOperationVersionException()
    {
        /** Prepare mock for _getOperationVersion() method. */
        $this->_requestMock->expects($this->once())
            ->method('getRequestedServices')
            ->will($this->returnValue(array('serviceName' => 'v1')));
        $this->_apiConfigMock->expects($this->once())
            ->method('getServiceNameByOperation')
            ->will($this->returnValue(false));
        $this->setExpectedException(
            'Magento_Webapi_Model_Soap_Fault',
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
        $this->_apiConfigMock->expects($this->once())
            ->method('getMethodNameByOperation')
            ->with($operation, '1')
            ->will($this->returnValue($method));
        $this->_apiConfigMock->expects($this->once())
            ->method('identifyVersionSuffix')
            ->with($operation, '1', $controllerMock)
            ->will($this->returnValue($versionAfterFallback));
        $this->_apiConfigMock->expects($this->once())
            ->method('checkDeprecationPolicy')
            ->with($service, $method, $versionAfterFallback);
        $arguments = reset($this->_arguments);
        $arguments = get_object_vars($arguments);
        $expectedResult = array('foo' => 'bar');
        $controllerMock->expects($this->once())
            ->method($action)
            ->with($arguments['customerId'])
            ->will($this->returnValue($expectedResult));

        /** Execute SUT. */
        $this->assertEquals(
            (object)array(Magento_Webapi_Controller_Soap_Handler::RESULT_NODE_NAME => $expectedResult),
            $this->_handler->__call($operation, $this->_arguments)
        );
    }

    /**
     * Process security header and prepare request arguments.
     */
    protected function _prepareSoapRequest()
    {
        /** Process security header by __call() method. */
        $this->_handler->setRequestHeaders(array(Magento_Webapi_Controller_Soap_Security::HEADER_SECURITY));
        $usernameToken = new stdClass();
        // @codingStandardsIgnoreStart
        $usernameToken->UsernameToken = new stdClass();
        $usernameToken->UsernameToken->Username = 'username';
        $usernameToken->UsernameToken->Password = 'password';
        $usernameToken->UsernameToken->Nonce = 'nonce';
        $usernameToken->UsernameToken->Created = 'created';
        // @codingStandardsIgnoreEnd
        $this->_handler->__call(
            Magento_Webapi_Controller_Soap_Security::HEADER_SECURITY,
            array($usernameToken)
        );

        /** Override arguments for process action header. */
        $request = new stdClass();
        $request->customerId = 1;
        $this->_arguments = array($request);
    }
}
