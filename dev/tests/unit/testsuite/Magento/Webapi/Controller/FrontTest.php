<?php
/**
 * Test Webapi Front Controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_FrontTest extends PHPUnit_Framework_TestCase
{
    const WEBAPI_AREA_FRONT_NAME = 'webapi';

    /** @var \Magento\Webapi\Controller\Front */
    protected $_frontControllerMock;

    /** @var \Magento\Controller\Router\Route\Factory. */
    protected $_routeFactoryMock;

    /** @var \Magento\Webapi\Controller\Dispatcher\Factory. */
    protected $_dispatcherFactory;

    /** @var \Magento\Webapi\Controller\Dispatcher\ErrorProcessor. */
    protected $_errorProcessorMock;

    /** @var \Magento\Core\Model\Config */
    protected $_configMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMockBuilder('Magento\Core\Model\Config')->disableOriginalConstructor()
            ->getMock();
        $this->_configMock->expects($this->any())->method('getAreaFrontName')->will(
            $this->returnValue(self::WEBAPI_AREA_FRONT_NAME)
        );

        $this->_dispatcherFactory = $this->getMockBuilder('Magento\Webapi\Controller\Dispatcher\Factory')
            ->disableOriginalConstructor()->getMock();
        $application = $this->getMockBuilder('Magento\Core\Model\App')->disableOriginalConstructor()->getMock();
        $application->expects($this->any())->method('getConfig')->will($this->returnValue($this->_configMock));

        $this->_routeFactoryMock = $this->getMockBuilder('Magento\Controller\Router\Route\Factory')
            ->disableOriginalConstructor()->getMock();
        $this->_errorProcessorMock = $this->getMockBuilder('Magento\Webapi\Controller\Dispatcher\ErrorProcessor')
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_frontControllerMock = new \Magento\Webapi\Controller\Front(
            $this->_dispatcherFactory,
            $application,
            $this->_routeFactoryMock,
            $this->_errorProcessorMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_frontControllerMock);
        unset($this->_errorProcessorMock);
        unset($this->_dispatcherFactory);
        unset($this->_routeFactoryMock);
        parent::tearDown();
    }

    public function testGetListOfAvailableApiTypes()
    {
        $expectedApiTypes = array('rest', 'soap');
        $this->assertEquals(
            $expectedApiTypes,
            $this->_frontControllerMock->getListOfAvailableApiTypes(),
            'Not expected API types.'
        );
    }

    /**
     * Exception throwing logic for testInitWithException method.
     *
     * @throws \Magento\Webapi\Exception
     */
    public function callbackThrowWebapiExcepion()
    {
        throw new \Magento\Webapi\Exception('Message', \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
    }

    /**
     * Test dispatch method.
     */
    public function testDispatch()
    {
        $this->_createMockForApiRouteAndFactory(array('api_type' => \Magento\Webapi\Controller\Front::API_TYPE_REST));
        $restDispatcherMock = $this->getMockBuilder('Magento\Webapi\Controller\Dispatcher\Rest')
            ->disableOriginalConstructor()
            ->getMock();
        /** Assert that handle method in mocked object will be executed only once. */
        $restDispatcherMock->expects($this->once())->method('dispatch');
        $this->_dispatcherFactory->expects($this->any())->method('get')
            ->will($this->returnValue($restDispatcherMock));
        $this->_frontControllerMock->dispatch();
    }

    /**
     * Test dispatch method with exception.
     */
    public function testDispatchException()
    {
        $this->_createMockForApiRouteAndFactory(array('api_type' => \Magento\Webapi\Controller\Front::API_TYPE_REST));
        $restDispatcherMock = $this->getMockBuilder('Magento\Webapi\Controller\Dispatcher\Rest')
            ->disableOriginalConstructor()
            ->getMock();
        /** Init Logical exception. */
        $logicalException = new LogicException();
        /** Mock dispatcher to throw Logical exception. */
        $restDispatcherMock->expects($this->any())->method('dispatch')->will($this->throwException($logicalException));
        $this->_dispatcherFactory->expects($this->any())->method('get')->will($this->returnValue($restDispatcherMock));
        /** Assert that error processor renderException method will be executed with Logical Exception. */
        $this->_errorProcessorMock->expects($this->once())->method('renderException')->with(
            $this->equalTo($logicalException)
        );
        $this->_frontControllerMock->dispatch();
    }

    /**
     * Test DetermineApiType method with Not defined API Type.
     */
    public function testDetermineApiTypeNotDefined()
    {
        $apiType = array('api_type' => 'invalidApiType');
        $this->_createMockForApiRouteAndFactory($apiType);
        /** Assert \Magento\Webapi\Exception type and message. */
        $this->setExpectedException(
            'Magento\Webapi\Exception',
            'The "invalidApiType" API type is not defined.',
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST
        );
        $this->_frontControllerMock->determineApiType();
    }

    /**
     * Test DeteminateApiType method without API Type specification.
     */
    public function testDetermineApiTypeInvalidRoute()
    {
        $apiType = false;
        $this->_createMockForApiRouteAndFactory($apiType);
        /** Assert \Magento\Webapi\Exception type and message. */
        $this->setExpectedException(
            'Magento\Webapi\Exception',
            'Request does not match any API type route.',
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST
        );
        $this->_frontControllerMock->determineApiType();
    }

    /**
     * Create mock for API Route and Route Factory objects.
     */
    protected function _createMockForApiRouteAndFactory($apiType)
    {
        $apiRouteMock = $this->getMockBuilder('Magento\Webapi\Controller\Router\Route')
            ->disableOriginalConstructor()->getMock();
        $apiRouteMock->expects($this->any())->method('match')->will($this->returnValue($apiType));
        $this->_routeFactoryMock->expects($this->any())->method('createRoute')->will(
            $this->returnValue($apiRouteMock)
        );
    }

    public function testDeterminateApiTypeApiIsSet()
    {
        $this->_createMockForApiRouteAndFactory(array('api_type' => \Magento\Webapi\Controller\Front::API_TYPE_SOAP));
        /** Assert that createRoute method will be executed only once */
        $this->_routeFactoryMock->expects($this->once())->method('createRoute');
        /** The first method call will set apiType property using createRoute method. */
        $this->_frontControllerMock->determineApiType();
        /** The second method call should use set apiType and should not trigger createRoute method. */
        $this->_frontControllerMock->determineApiType();
    }
}
