<?php
/**
 * Test Rest controller dispatcher.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Dispatcher_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Controller_Dispatcher_Rest */
    protected $_restDispatcher;

    /** @var Magento_Webapi_Controller_Dispatcher_Rest_Authentication */
    protected $_authenticationMock;

    /** @var Magento_Webapi_Controller_Response_Rest */
    protected $_responseMock;

    /** @var Magento_Webapi_Controller_Router_Rest */
    protected $_routerMock;

    /** @var Magento_Webapi_Controller_Action_Factory */
    protected $_controllerFactory;

    /** @var Magento_Webapi_Model_Config_Rest */
    protected $_apiConfigMock;

    /** @var Magento_Webapi_Model_Authorization */
    protected $_authorizationMock;

    /** @var Magento_Webapi_Controller_Dispatcher_Rest_Presentation */
    protected $_restPresentation;

    protected function setUp()
    {
        /** Init dependencies for SUT. */
        $this->_apiConfigMock = $this->getMockBuilder('Magento_Webapi_Model_Config_Rest')->disableOriginalConstructor()
            ->getMock();
        $requestMock = $this->getMockBuilder('Magento_Webapi_Controller_Request_Rest')->disableOriginalConstructor()
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Magento_Webapi_Controller_Response_Rest')
            ->disableOriginalConstructor()->getMock();
        $this->_controllerFactory = $this->getMockBuilder('Magento_Webapi_Controller_Action_Factory')
            ->disableOriginalConstructor()->getMock();
        $this->_restPresentation = $this->getMockBuilder('Magento_Webapi_Controller_Dispatcher_Rest_Presentation')
            ->disableOriginalConstructor()->getMock();
        $this->_routerMock = $this->getMockBuilder('Magento_Webapi_Controller_Router_Rest')
            ->disableOriginalConstructor()->getMock();
        $this->_authorizationMock = $this->getMockBuilder('Magento_Webapi_Model_Authorization')
            ->disableOriginalConstructor()->getMock();
        $this->_authenticationMock = $this->getMockBuilder('Magento_Webapi_Controller_Dispatcher_Rest_Authentication')
            ->disableOriginalConstructor()->getMock();

        /** Init SUT. */
        $this->_restDispatcher = new Magento_Webapi_Controller_Dispatcher_Rest(
            $this->_apiConfigMock,
            $requestMock,
            $this->_responseMock,
            $this->_controllerFactory,
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
        unset($this->_controllerFactory);
        unset($this->_apiConfigMock);
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
        $routeMock = $this->getMockBuilder('Magento_Webapi_Controller_Router_Route_Rest')->disableOriginalConstructor()
            ->getMock();
        $routeMock->expects($this->any())->method('getResourceName');
        $this->_routerMock->expects($this->once())->method('match')->will($this->returnValue($routeMock));
        /** Mock Api Config getMethodNameByOperation method to return isDeleted method of Magento_Object. */
        $this->_apiConfigMock->expects($this->once())->method('getMethodNameByOperation')->will(
            $this->returnValue('isDeleted')
        );
        /** Mock Api config identifyVersionSuffix method to return empty string. */
        $this->_apiConfigMock->expects($this->once())->method('identifyVersionSuffix')->will($this->returnValue(''));
        $this->_apiConfigMock->expects($this->once())->method('checkDeprecationPolicy');
        $this->_authorizationMock->expects($this->once())->method('checkResourceAcl');
        /** Create fake controller mock, e.g., Magento_Object object. */
        $controllerMock = $this->getMockBuilder('Magento_Object')->disableOriginalConstructor()->getMock();
        /** Assert that isDeleted method will be executed once. */
        $controllerMock->expects($this->once())->method('isDeleted');
        /** Mock factory mock to return fake action controller. */
        $this->_controllerFactory->expects($this->once())->method('createActionController')->will(
            $this->returnValue($controllerMock)
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
