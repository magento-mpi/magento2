<?php
/**
 * Test REST API dispatcher.
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

    /** @var Mage_Webapi_Controller_Response_Rest */
    protected $_responseMock;

    /** @var Mage_Webapi_Controller_Router_Rest */
    protected $_routerMock;

    /** @var Mage_Core_Service_Factory */
    protected $_serviceFactory;

    /** @var Mage_Core_Service_Config */
    protected $_serviceConfig;

    /** @var Mage_Webapi_Model_Authorization */
    protected $_authorizationMock;

    /** @var Mage_Webapi_Controller_Dispatcher_Rest_Presentation */
    protected $_restPresentation;

    protected function setUp()
    {
        /** Init dependencies for SUT. */
        $this->_serviceConfig = $this->getMockBuilder('Mage_Core_Service_Config')->disableOriginalConstructor()
            ->getMock();
        $requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Request_Rest')->disableOriginalConstructor()
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Mage_Webapi_Controller_Response_Rest')
            ->disableOriginalConstructor()->getMock();
        $this->_serviceFactory = $this->getMockBuilder('Mage_Core_Service_Factory')
            ->disableOriginalConstructor()->getMock();
        $this->_restPresentation = $this->getMockBuilder('Mage_Webapi_Controller_Dispatcher_Rest_Presentation')
            ->disableOriginalConstructor()->getMock();
        $this->_routerMock = $this->getMockBuilder('Mage_Webapi_Controller_Router_Rest')->disableOriginalConstructor()
            ->getMock();
        $this->_authorizationMock = $this->getMockBuilder('Mage_Webapi_Model_Authorization')
            ->disableOriginalConstructor()->getMock();
        $this->_authenticationMock = $this->getMockBuilder('Mage_Webapi_Controller_Dispatcher_Rest_Authentication')
            ->disableOriginalConstructor()->getMock();

        /** Init SUT. */
        $this->_restDispatcher = new Mage_Webapi_Controller_Dispatcher_Rest(
            $this->_serviceConfig,
            $requestMock,
            $this->_responseMock,
            $this->_serviceFactory,
            $this->_restPresentation,
            $this->_routerMock,
            $this->_authorizationMock,
            $this->_authenticationMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_restDispatcher);
        unset($this->_authenticationMock);
        unset($this->_responseMock);
        unset($this->_routerMock);
        unset($this->_serviceFactory);
        unset($this->_serviceConfig);
        unset($this->_authorizationMock);
        unset($this->_restPresentation);
        parent::tearDown();
    }

    /**
     * Test dispatch method with Exception throwing.
     */
    public function testDispatchException()
    {
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
        $this->_authenticationMock->expects($this->once())->method('authenticate');
        /** Init route mock. */
        $routeMock = $this->getMockBuilder('Mage_Webapi_Controller_Router_Route_Rest')->disableOriginalConstructor()
            ->getMock();
        $routeMock->expects($this->any())->method('getServiceName');
        $this->_routerMock->expects($this->once())->method('match')->will($this->returnValue($routeMock));

        $this->_serviceConfig->expects($this->once())->method('checkDeprecationPolicy');
        // $this->_authorizationMock->expects($this->once())->method('checkResourceAcl');
        /** Create fake service mock, e.g., Varien_Object object. */
        $serviceMock = $this->getMockBuilder('Varien_Object')->disableOriginalConstructor()->getMock();
        /** Mock factory mock to return fake service. */
        $this->_serviceFactory->expects($this->once())->method('createServiceInstance')->will(
            $this->returnValue($serviceMock)
        );
        /** Mock Rest presentation fetchRequestData method to return empty array. */
        $this->_restPresentation->expects($this->once())->method('fetchRequestData')->will(
            $this->returnValue(array())
        );
        /** Assert that response sendResponse method will be executed once. */
        $this->_responseMock->expects($this->once())->method('sendResponse');

        $this->_restDispatcher->dispatch();
    }
}
