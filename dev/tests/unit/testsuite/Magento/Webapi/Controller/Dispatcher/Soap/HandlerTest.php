<?php
/**
 * Test for \Magento\Webapi\Controller\Dispatcher\Soap\Handler.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Dispatcher_Soap_HandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Dispatcher\Soap\Handler */
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
        /** Prepare mocks for SUT constructor. */
        $this->_apiConfigMock = $this->getMockBuilder('Magento\Webapi\Model\Config\Soap')
            ->setMethods(
                array(
                    'getResourceNameByOperation',
                    'validateVersionNumber',
                    'getControllerClassByOperationName',
                    'getMethodNameByOperation',
                    'identifyVersionSuffix',
                    'checkDeprecationPolicy'
                )
            )->disableOriginalConstructor()
            ->getMock();
        $this->_helperMock = $this->getMockBuilder('Magento\Webapi\Helper\Data')
            ->setMethods(array('prepareMethodParams'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_authenticationMock = $this->getMockBuilder('Magento\Webapi\Controller\Dispatcher\Soap\Authentication')
            ->setMethods(array('authenticate'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_factoryMock = $this->getMockBuilder('Magento\Webapi\Controller\Action\Factory')
            ->setMethods(array('createActionController'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_authorizationMock = $this->getMockBuilder('Magento\Webapi\Model\Authorization')
            ->setMethods(array('checkResourceAcl'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_requestMock = $this->getMockBuilder('Magento\Webapi\Controller\Request\Soap')
            ->setMethods(array('getRequestedResources'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_errorProcessorMock = $this->getMockBuilder('Magento\Webapi\Controller\Dispatcher\ErrorProcessor')
            ->setMethods(array('maskException'))
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_handler = new \Magento\Webapi\Controller\Dispatcher\Soap\Handler(
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
        $this->setExpectedException(
            '\Magento\Webapi\Model\Soap\Fault',
            'WS-Security UsernameToken is not found in SOAP-request.'
        );
        /** Execute SUT. */
        $this->_handler->__call('operation', array());
    }

    /**
     * This test is checking any other exceptions but \Magento\Webapi\Exception handling during __call().
     */
    public function testCallException()
    {
        /** Prepare mocks for SUT constructor. */
        $exceptionMessage = 'Exception message.';
        $this->setExpectedException(
            '\Magento\Webapi\Model\Soap\Fault',
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
            ->method('getRequestedResources')
            ->will($this->returnValue(array('resourceName' => 'v1')));
        /** Create the arguments map of returned values for getResourceNameByOperation() method. */
        $getResourceValueMap = array(
            array('operation', null, 'resourceName'),
            array('operation', 'v1', false)
        );
        $this->_apiConfigMock->expects($this->any())
            ->method('getResourceNameByOperation')
            ->will($this->returnValueMap($getResourceValueMap));
        $this->_apiConfigMock->expects($this->once())
            ->method('validateVersionNumber')
            ->with(1, 'resourceName');
        $this->setExpectedException(
            '\Magento\Webapi\Model\Soap\Fault',
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
            ->method('getRequestedResources')
            ->will($this->returnValue(array('resourceName' => 'v1')));
        $this->_apiConfigMock->expects($this->once())
            ->method('getResourceNameByOperation')
            ->will($this->returnValue(false));
        $this->setExpectedException(
            '\Magento\Webapi\Model\Soap\Fault',
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
        $resource = 'resourceName';
        $operation = $resource . $method;
        $this->_authenticationMock->expects($this->once())->method('authenticate');
        $this->_requestMock->expects($this->once())
            ->method('getRequestedResources')
            ->will($this->returnValue(array($resource => 'v1')));
        $this->_apiConfigMock->expects($this->any())
            ->method('getResourceNameByOperation')
            ->will($this->returnValue($resource));
        $this->_apiConfigMock->expects($this->once())
            ->method('validateVersionNumber')
            ->with(1, $resource);
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
            ->method('checkResourceAcl')
            ->with($resource, $method);
        $this->_apiConfigMock->expects($this->once())
            ->method('identifyVersionSuffix')
            ->with($operation, '1', $controllerMock)
            ->will($this->returnValue($versionAfterFallback));
        $this->_apiConfigMock->expects($this->once())
            ->method('checkDeprecationPolicy')
            ->with($resource, $method, $versionAfterFallback);
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
            (object)array(\Magento\Webapi\Controller\Dispatcher\Soap\Handler::RESULT_NODE_NAME => $expectedResult),
            $this->_handler->__call($operation, $this->_arguments)
        );
    }

    /**
     * Process security header and prepare request arguments.
     */
    protected function _prepareSoapRequest()
    {
        /** Process security header by __call() method. */
        $this->_handler->setRequestHeaders(array(\Magento\Webapi\Controller\Dispatcher\Soap\Handler::HEADER_SECURITY));
        $usernameToken = new stdClass();
        // @codingStandardsIgnoreStart
        $usernameToken->UsernameToken = new stdClass();
        $usernameToken->UsernameToken->Username = 'username';
        $usernameToken->UsernameToken->Password = 'password';
        $usernameToken->UsernameToken->Nonce = 'nonce';
        $usernameToken->UsernameToken->Created = 'created';
        // @codingStandardsIgnoreEnd
        $this->_handler->__call(
            \Magento\Webapi\Controller\Dispatcher\Soap\Handler::HEADER_SECURITY,
            array($usernameToken)
        );

        /** Override arguments for process action header. */
        $request = new stdClass();
        $request->customerId = 1;
        $this->_arguments = array($request);
    }
}
