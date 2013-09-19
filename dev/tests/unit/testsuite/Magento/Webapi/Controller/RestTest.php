<?php
/**
 * Test Rest controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Controller_Rest */
    protected $_restController;

    /** @var Magento_Webapi_Controller_Rest_Request */
    protected $_requestMock;

    /** @var Magento_Webapi_Controller_Rest_Response */
    protected $_responseMock;

    /** @var Magento_Webapi_Controller_Rest_Router */
    protected $_routerMock;

    /** @var Magento_Webapi_Controller_Rest_Router_Route */
    protected $_routeMock;

    /** @var Magento_ObjectManager */
    protected $_objectManagerMock;

    /** @var stdClass */
    protected $_serviceMock;

    /** @var Magento_Core_Model_App_State */
    protected $_appStateMock;

    /** @var Magento_Webapi_Model_Authentication */
    protected $_authenticationMock;

    const SERVICE_METHOD = Magento_Webapi_Model_Rest_Config::KEY_METHOD;
    const SERVICE_ID = Magento_Webapi_Model_Rest_Config::KEY_CLASS;

    protected function setUp()
    {
        $this->_requestMock = $this->getMockBuilder('Magento_Webapi_Controller_Rest_Request')
            ->setMethods(array('isSecure', 'getRequestData'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_responseMock = $this->getMockBuilder('Magento_Webapi_Controller_Rest_Response')
            ->setMethods(array('sendResponse', 'getHeaders', 'prepareResponse'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_routerMock = $this->getMockBuilder('Magento_Webapi_Controller_Rest_Router')
            ->setMethods(array('match'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_routeMock = $this->getMockBuilder('Magento_Webapi_Controller_Rest_Router_Route')
            ->setMethods(array('isSecure', 'getServiceMethod', 'getServiceClass'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_serviceMock = $this->getMockBuilder('stdClass')
            ->setMethods(array(self::SERVICE_METHOD))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_appStateMock =  $this->getMockBuilder('Magento_Core_Model_App_State')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_authenticationMock = $this->getMockBuilder('Magento_Webapi_Model_Authentication')
            ->disableOriginalConstructor()
            ->getMock();

        /** Init SUT. */
        $this->_restController = new Magento_Webapi_Controller_Rest(
            $this->_requestMock,
            $this->_responseMock,
            $this->_routerMock,
            $this->_objectManagerMock,
            $this->_appStateMock,
            $this->_authenticationMock
        );

        // Set default expectations used by all tests
        $this->_routeMock
            ->expects($this->any())->method('getServiceClass')->will($this->returnValue(self::SERVICE_ID));

        $this->_routeMock
            ->expects($this->any())->method('getServiceMethod')->will($this->returnValue(self::SERVICE_METHOD));
        $this->_routerMock->expects($this->any())->method('match')->will($this->returnValue($this->_routeMock));

        $this->_objectManagerMock->expects($this->any())->method('get')->will($this->returnValue($this->_serviceMock));
        $this->_responseMock->expects($this->any())->method('prepareResponse')->will($this->returnValue(array()));
        $this->_requestMock->expects($this->any())->method('getRequestData')->will($this->returnValue(array()));

        /** Assert that response sendResponse method will be executed once. */
        $this->_responseMock->expects($this->once())->method('sendResponse');

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_restController);
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_routerMock);
        unset($this->_objectManagerMock);
        unset($this->_authenticationMock);
        unset($this->_appStateMock);
        parent::tearDown();
    }

    /**
     * Test redirected to install page
     */
    public function testRedirectToInstallPage()
    {
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(false));
        $expectedMsg = 'Magento is not yet installed';

        $this->_restController->dispatch();
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals($expectedMsg, $exceptionArray[0]->getMessage());
    }

    /**
     * Test dispatch method with Exception throwing.
     */
    public function testDispatchAuthenticationException()
    {
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));
        $this->_serviceMock->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue(array()));
        $this->markTestIncomplete(
            "Test should be fixed after Magento_Webapi_Controller_Rest::dispatch() enforces authentication"
        );
    }

    /**
     * Test Secure Request and Secure route combinations
     *
     * @dataProvider dataProviderSecureRequestSecureRoute
     */
    public function testSecureRouteAndRequest($isSecureRoute, $isSecureRequest)
    {
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));
        $this->_serviceMock
            ->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue(array()));
        $this->_routeMock->expects($this->any())->method('isSecure')->will($this->returnValue($isSecureRoute));
        $this->_requestMock->expects($this->any())->method('isSecure')->will($this->returnValue($isSecureRequest));
        $this->_restController->dispatch();
        $this->assertFalse($this->_responseMock->isException());
    }

    /**
     * Data provider for testSecureRouteAndRequest.
     *
     * @return array
     */
    public function dataProviderSecureRequestSecureRoute()
    {
        return array(
            //Each array contains return type for isSecure method of route and request objects .
            array(
                true,
                true
            ),
            array(
                false,
                true
            ),
            array(
                false,
                false
            )
        );

    }

    /**
     * Test insecure request for a secure route
     */
    public function testInSecureRequestOverSecureRoute()
    {
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));
        $this->_serviceMock->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue(array()));
        $this->_routeMock->expects($this->any())->method('isSecure')->will($this->returnValue(true));
        $this->_requestMock->expects($this->any())->method('isSecure')->will($this->returnValue(false));

        // Override default prepareResponse. It should never be called in this case
        $this->_responseMock->expects($this->never())->method('prepareResponse');

        $this->_restController->dispatch();
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals('Operation allowed only in HTTPS', $exceptionArray[0]->getMessage());
        $this->assertEquals(Magento_Webapi_Exception::HTTP_BAD_REQUEST, $exceptionArray[0]->getHttpCode());
    }

    /**
     * Test incorrect format type response from service methods
     */
    public function testInvalidReturnTypeFromService()
    {
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));
        $this->_serviceMock->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue("invalid"));
        $this->_routeMock->expects($this->any())->method('isSecure')->will($this->returnValue(false));
        $this->_requestMock->expects($this->any())->method('isSecure')->will($this->returnValue(false));

        // Override default prepareResponse. It should never be called in this case
        $this->_responseMock->expects($this->never())->method('prepareResponse');

        $expectedMsg = 'The method "' . self::SERVICE_METHOD . '" of service "'
            . self::SERVICE_ID . '" must return an array.';

        $this->_restController->dispatch();
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals($expectedMsg, $exceptionArray[0]->getMessage());
    }
}
