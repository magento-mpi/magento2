<?php
/**
 * Test Rest controller dispatcher.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Dispatcher_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Dispatcher_Rest */
    protected $_restDispatcher;

    /** @var Mage_Webapi_Controller_Dispatcher_Rest_Authentication */
    protected $_authenticationMock;

    /** @var Mage_Webapi_Controller_Request_Rest */
    protected $_requestMock;

    /** @var Mage_Webapi_Controller_Response_Rest */
    protected $_responseMock;

    /** @var Mage_Webapi_Controller_Router_Rest */
    protected $_routerMock;

    /** @var Mage_Webapi_Controller_Dispatcher_Rest_Presentation */
    protected $_restPresentation;

    /** @var Magento_ObjectManager */
    protected $_objectManagerMock;

    protected function setUp()
    {
        /** Init dependencies for SUT. */
        $this->_apiConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Config_Rest')->disableOriginalConstructor()
            ->getMock();
        $this->_requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Request_Rest')->disableOriginalConstructor()
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Mage_Webapi_Controller_Response_Rest')
            ->disableOriginalConstructor()->getMock();
        $this->_restPresentation = $this->getMockBuilder('Mage_Webapi_Controller_Dispatcher_Rest_Presentation')
            ->disableOriginalConstructor()->getMock();
        $this->_routerMock = $this->getMockBuilder('Mage_Webapi_Controller_Router_Rest')->disableOriginalConstructor()
            ->getMock();
        $this->_authenticationMock = $this->getMockBuilder('Mage_Webapi_Controller_Dispatcher_Rest_Authentication')
            ->disableOriginalConstructor()->getMock();

        $this->_objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()->getMock();

        /** Init SUT. */
        $this->_restDispatcher = new Mage_Webapi_Controller_Dispatcher_Rest(
            $this->_requestMock,
            $this->_responseMock,
            $this->_restPresentation,
            $this->_routerMock,
            $this->_authenticationMock,
            $this->_objectManagerMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_restDispatcher);
        unset($this->_authenticationMock);
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_routerMock);
        unset($this->_objectManagerMock);
        unset($this->_authorizationMock);
        unset($this->_restPresentation);
        parent::tearDown();
    }

    /**
     * Test dispatch method with Exception throwing.
     */
    public function testDispatchException()
    {
        $this->markTestIncomplete(
            "Test should be fixed after Mage_Webapi_Controller_Dispatcher_Rest::dispatch() is complete"
        );
        /** Init logical Exception. */
        $logicalException = new LogicException();
        /** Mock authenticate method to throw Exception. */
        $this->_authenticationMock->expects($this->once())->method('authenticate')->will(
            $this->throwException($logicalException)
        );
        /** Assert that setException method will be executed with thrown logical Exception. */
        $this->_responseMock->expects($this->once())->method('setException')->with($this->equalTo($logicalException));

        $this->_restDispatcher->dispatch();
    }

    /**
     * Test dispatch method.
     */
    public function testDispatch()
    {
        //$this->_authenticationMock->expects($this->once())->method('authenticate');
        /** Init route mock. */
        $routeMock = $this->getMockBuilder('Mage_Webapi_Controller_Router_Route_Rest')->disableOriginalConstructor()
            ->getMock();
        $routeMock->expects($this->any())->method('getServiceId');
        $serviceMethodName = 'getServiceMethod';
        $routeMock->expects($this->any())->method('getServiceMethod')->will($this->returnValue($serviceMethodName));
        $routeMock->expects($this->any())->method('getServiceVersion');
        $this->_routerMock->expects($this->once())->method('match')->will($this->returnValue($routeMock));
        $serviceMock = $this->getMockBuilder('Varien_Object')->disableOriginalConstructor()->getMock();
        $serviceMock->expects($this->any())->method($serviceMethodName)->will($this->returnValue(array()));
        $this->_objectManagerMock->expects($this->once())->method('get')->will(
            $this->returnValue($serviceMock)
        );
        $this->_restPresentation->expects($this->once())->method('prepareResponse')->will(
            $this->returnValue(array())
        );
        /** Assert that response sendResponse method will be executed once. */
        $this->_responseMock->expects($this->once())->method('sendResponse');

        $this->_restDispatcher->dispatch();
    }

    /**
     * Test dispatch for a secure operation
     */
    public function testSecureOperation()
    {
        //$this->_authenticationMock->expects($this->once())->method('authenticate');
        /** Init route mock. */
        $routeMock = $this->getMockBuilder('Mage_Webapi_Controller_Router_Route_Rest')->disableOriginalConstructor()
            ->getMock();
        $routeMock->expects($this->any())->method('getServiceId');
        $routeMock->expects($this->any())->method('getServiceMethod');
        $routeMock->expects($this->any())->method('getServiceVersion');
        $routeMock->expects($this->any())->method('isSecure')->will($this->returnValue(true));
        $this->_routerMock->expects($this->once())->method('match')->will($this->returnValue($routeMock));
        $this->_serviceManagerMock->expects($this->once())->method('call')->will($this->returnValue(array()));
        $this->_restPresentation->expects($this->once())->method('prepareResponse')->will(
            $this->returnValue(array())
        );
        $this->_requestMock->expects($this->any())->method("isSecure")->will($this->returnValue(true));
        /** Assert that response sendResponse method will be executed once. */
        $this->_responseMock->expects($this->once())->method('sendResponse');

        $this->_restDispatcher->dispatch();
    }

    /**
     * Test dispatch for a secure operation when call is made in insecure channel
     */
    public function testSecureOperationDispatchForInsecureCall()
    {
        //$this->_authenticationMock->expects($this->once())->method('authenticate');
        /** Init route mock. */
        $routeMock = $this->getMockBuilder('Mage_Webapi_Controller_Router_Route_Rest')->disableOriginalConstructor()
            ->getMock();
        $routeMock->expects($this->any())->method('getServiceId');
        $routeMock->expects($this->any())->method('getServiceMethod');
        $routeMock->expects($this->any())->method('getServiceVersion');
        $routeMock->expects($this->any())->method('isSecure')->will($this->returnValue(true));
        $this->_routerMock->expects($this->once())->method('match')->will($this->returnValue($routeMock));

        /** Assert that setException method will be executed once. */
        $this->_responseMock->expects($this->once())->method('setException')->with(
            new Exception("Operation allowed only in HTTPS", 40001)
        );

        $this->_restDispatcher->dispatch();
    }
}
