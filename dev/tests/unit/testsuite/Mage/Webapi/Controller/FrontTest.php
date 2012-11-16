<?php
/**
 * Test Webapi Front Controller
 *
 * @copyright {}
 */

class Mage_Webapi_Controller_FrontTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Front */
    protected $_frontControllerMock;

    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactoryMock;

    /** @var Mage_Webapi_Controller_Handler_Factory */
    protected $_handlerFactoryMock;

    /** @var Mage_Webapi_Controller_Handler_ErrorProcessor */
    protected $_errorProcessorMock;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $helper = $this->getMock('Mage_Webapi_Helper_Data', array('__'));
        $helper->expects($this->any())->method('__')->will($this->returnArgument(0));
        $helperFactory = $this->getMock('Mage_Core_Model_Factory_Helper');
        $helperFactory->expects($this->any())->method('get')->will($this->returnValue($helper));

        $this->_handlerFactoryMock = $this->getMockBuilder('Mage_Webapi_Controller_Handler_Factory')
            ->disableOriginalConstructor()->getMock();
        $application = $this->getMockBuilder('Mage_Core_Model_App')->disableOriginalConstructor()->getMock();
        $this->_routeFactoryMock = $this->getMockBuilder('Magento_Controller_Router_Route_Factory')
            ->disableOriginalConstructor()->getMock();
        $this->_errorProcessorMock = $this->getMockBuilder('Mage_Webapi_Controller_Handler_ErrorProcessor')
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_frontControllerMock = new Mage_Webapi_Controller_Front(
            $helperFactory,
            $this->_handlerFactoryMock,
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
        unset($this->_handlerFactoryMock);
        unset($this->_handlerFactoryMock);
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
     * Test Init Front Controller method
     */
    public function testInit()
    {
        /** Prepare test data */
        $this->_createMockForApiRouteAndFactory(array('api_type' => Mage_Webapi_Controller_Front::API_TYPE_REST));
        $restHandlerMock = $this->getMockBuilder('Mage_Webapi_Controller_Handler_Rest')->disableOriginalConstructor()
            ->getMock();
        /** Assert init method in mocked object will run once */
        $restHandlerMock->expects($this->once())->method('init');
        $this->_handlerFactoryMock->expects($this->any())->method('get')->will($this->returnValue($restHandlerMock));
        $this->_frontControllerMock->init();
    }

    /**
     * Test Exception processing during Init
     */
    public function testInitWithException()
    {
        $this->markTestSkipped('Consider die() refactor in Mage_Webapi_Controller_Front->Init method');
        $this->_createMockForApiRouteAndFactory(array('api_type' => Mage_Webapi_Controller_Front::API_TYPE_REST));
        $restHandlerMock = $this->getMockBuilder('Mage_Webapi_Controller_Handler_Rest')->disableOriginalConstructor()
            ->getMock();
        /** Init method will throw Exception in callbackThrowWebapiExcepion function */
        $restHandlerMock->expects($this->any())->method('init')->will(
            $this->returnCallback(array($this, 'callbackThrowWebapiExcepion'))
        );
        $this->_handlerFactoryMock->expects($this->any())->method('get')->will($this->returnValue($restHandlerMock));
        /** Assert render method in mocked object will run once */
        $this->_errorProcessorMock->expects($this->once())->method('render');
        $this->_frontControllerMock->init();
    }

    /**
     * Exception throwing logic for testInitWithException method
     *
     * @throws Mage_Webapi_Exception
     */
    public function callbackThrowWebapiExcepion()
    {
        throw new Mage_Webapi_Exception('Message', Mage_Webapi_Exception::HTTP_BAD_REQUEST);
    }

    /**
     * Test dispatch method
     */
    public function testDispatch()
    {
        $this->_createMockForApiRouteAndFactory(array('api_type' => Mage_Webapi_Controller_Front::API_TYPE_REST));
        $restHandlerMock = $this->getMockBuilder('Mage_Webapi_Controller_Handler_Rest')->disableOriginalConstructor()
            ->getMock();
        /** Assert handle method in mocked object will run once */
        $restHandlerMock->expects($this->once())->method('handle');
        $this->_handlerFactoryMock->expects($this->any())->method('get')->will($this->returnValue($restHandlerMock));
        $this->_frontControllerMock->dispatch();
    }

    /**
     * Test DetermineApiType method with Not defined API Type
     */
    public function testDetermineApiTypeNotDefined()
    {
        $apiType = array('api_type' => 'invalidApiType');
        $this->_createMockForApiRouteAndFactory($apiType);
        /** Assert Mage_Webapi_Exception type and message */
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'The "%s" API type is not defined.',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        $this->_frontControllerMock->determineApiType();
    }

    /**
     * Test DeteminateApiType method without API Type specification
     */
    public function testDetermineApiTypeInvalidRoute()
    {
        $apiType = false;
        $this->_createMockForApiRouteAndFactory($apiType);
        /** Assert Mage_Webapi_Exception type and message */
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Request does not match any API type route.',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        $this->_frontControllerMock->determineApiType();
    }

    /**
     * Create mock for API Route and Route Factory objects
     */
    protected function _createMockForApiRouteAndFactory($apiType)
    {
        $apiRouteMock = $this->getMockBuilder('Mage_Webapi_Controller_Router_Route_Webapi')->disableOriginalConstructor(
        )->getMock();
        $apiRouteMock->expects($this->any())->method('match')->will($this->returnValue($apiType));
        $this->_routeFactoryMock->expects($this->any())->method('createRoute')->will(
            $this->returnValue($apiRouteMock)
        );
    }

    /**
     * Test protected var $apiType is set on first call of DeterminateApiType method
     */
    public function testDeterminateApiTypeApiIsSet()
    {
        $this->_createMockForApiRouteAndFactory(array('api_type' => Mage_Webapi_Controller_Front::API_TYPE_SOAP));
        /** Assert createRoute method will run only once */
        $this->_routeFactoryMock->expects($this->once())->method('createRoute');
        $this->_frontControllerMock->determineApiType();
        $this->_frontControllerMock->determineApiType();
    }
}
