<?php
/**
 * Test Rest controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Rest */
    protected $_restDispatcher;

    /** @var Mage_Webapi_Controller_Rest_Authentication */
    protected $_authenticationMock;

    /** @var Mage_Webapi_Controller_Rest_Request */
    protected $_requestMock;

    /** @var Mage_Webapi_Controller_Rest_Response */
    protected $_responseMock;

    /** @var Mage_Webapi_Controller_Rest_Router */
    protected $_routerMock;

    /** @var Mage_Webapi_Controller_Rest_Router_Route */
    protected $_routeMock;

    /** @var Mage_Webapi_Controller_Rest_Presentation */
    protected $_restPresentation;

    /** @var Magento_ObjectManager */
    protected $_objectManagerMock;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helperMock;

    /** @var stdClass */
    protected $_serviceMock;

    const SERVICE_METHOD = 'serviceMethod';
    const SERVICE_ID = 'serviceId';

    protected function setUp()
    {
        $this->_requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Request')
            ->setMethods(array('isSecure'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_responseMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Response')
            ->setMethods(array('sendResponse'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_restPresentation = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Presentation')
            ->setMethods(array('prepareResponse', 'getRequestData'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_routerMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Router')
            ->setMethods(array('match'))
            ->disableOriginalConstructor()
            ->getMock();


        $this->_routeMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Router_Route')
            ->setMethods(array('isSecure', 'getServiceMethod', 'getServiceId', 'getServiceVersion'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_authenticationMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Authentication')
            ->setMethods(array('isSecure'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_serviceMock = $this->getMockBuilder('stdClass')
            ->setMethods(array(self::SERVICE_METHOD))
            ->disableOriginalConstructor()
            ->getMock();


        /** Init SUT. */
        $this->_restDispatcher = new Mage_Webapi_Controller_Rest(
            $this->_requestMock,
            $this->_responseMock,
            $this->_restPresentation,
            $this->_routerMock,
            $this->_authenticationMock,
            $this->_objectManagerMock,
            $this->_helperMock
        );


        //Set default expectations used by all tests
        $this->_routeMock
            ->expects($this->any())->method('getServiceId')->will($this->returnValue(self::SERVICE_ID));

        $this->_routeMock
            ->expects($this->any())->method('getServiceMethod')->will($this->returnValue(self::SERVICE_METHOD));
        $this->_routeMock->expects($this->any())->method('getServiceVersion');
        $this->_routerMock->expects($this->any())->method('match')->will($this->returnValue($this->_routeMock));

        $this->_objectManagerMock->expects($this->any())->method('get')->will($this->returnValue($this->_serviceMock));
        $this->_restPresentation->expects($this->any())->method('prepareResponse')->will($this->returnValue(array()));
        $this->_restPresentation->expects($this->any())->method('getRequestData')->will($this->returnValue(array()));

        /** Assert that response sendResponse method will be executed once. */
        $this->_responseMock->expects($this->any())->method('sendResponse');

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
        unset($this->_helperMock);
        parent::tearDown();
    }

    /**
     * Test dispatch method with Exception throwing.
     */
    public function testDispatchAuthenticationException()
    {
        $this->_serviceMock->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue(array()));
        $this->markTestIncomplete(
            "Test should be fixed after Mage_Webapi_Controller_Rest::dispatch() enforces authentication"
        );
    }

    /**
     * Test Secure Request and Secure route combinations
     *
     * @dataProvider dataProviderSecureRequestSecureRoute
     */
    public function testSecureRouteAndRequest($isSecureRoute, $isSecureRequest)
    {
        $this->_serviceMock
            ->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue(array()));
        $this->_routeMock->expects($this->any())->method('isSecure')->will($this->returnValue($isSecureRoute));
        $this->_requestMock->expects($this->any())->method('isSecure')->will($this->returnValue($isSecureRequest));
        $this->_restDispatcher->dispatch();
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
        $this->_serviceMock->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue(array()));
        $this->_routeMock->expects($this->any())->method('isSecure')->will($this->returnValue(true));
        $this->_requestMock->expects($this->any())->method('isSecure')->will($this->returnValue(false));

        //Override default prepareResponse. It should never be called in this case
        $this->_restPresentation->expects($this->never())->method('prepareResponse');

        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));

        $this->_restDispatcher->dispatch();
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals('Operation allowed only in HTTPS', $exceptionArray[0]->getMessage());
    }

    /**
     * Test incorrect format type response from service methods
     */
    public function testInvalidReturnTypeFromService()
    {
        $this->_serviceMock->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue("invalid"));
        $this->_routeMock->expects($this->any())->method('isSecure')->will($this->returnValue(false));
        $this->_requestMock->expects($this->any())->method('isSecure')->will($this->returnValue(false));

        //Override default prepareResponse. It should never be called in this case
        $this->_restPresentation->expects($this->never())->method('prepareResponse');

        $expectedMsg = 'The method' . self::SERVICE_METHOD . ' of service '
            . self::SERVICE_ID . ' must return an array.';
        $this->_helperMock->expects($this->any())
            ->method('__')
            ->with('The method "%s" of service "%s" must return an array.', self::SERVICE_METHOD, self::SERVICE_ID)
            ->will($this->returnValue($expectedMsg));

        $this->_restDispatcher->dispatch();
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals($expectedMsg, $exceptionArray[0]->getMessage());
    }

}
