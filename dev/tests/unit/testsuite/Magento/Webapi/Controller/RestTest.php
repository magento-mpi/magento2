<?php
/**
 * Test Rest controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

class RestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Rest */
    protected $_restController;

    /** @var \Magento\Webapi\Controller\Rest\Request */
    protected $_requestMock;

    /** @var \Magento\Webapi\Controller\Rest\Response */
    protected $_responseMock;

    /** @var \Magento\Webapi\Controller\Rest\Router */
    protected $_routerMock;

    /** @var \Magento\Webapi\Controller\Rest\Router\Route */
    protected $_routeMock;

    /** @var \Magento\ObjectManager */
    protected $_objectManagerMock;

    /** @var \stdClass */
    protected $_serviceMock;

    /** @var \Magento\App\State */
    protected $_appStateMock;

    /** @var \Magento\Oauth\Oauth */
    protected $_oauthServiceMock;

    /** @var \Magento\Oauth\Helper\Request */
    protected $_oauthHelperMock;

    /** @var \Magento\Authz\Service\AuthorizationV1Interface */
    protected $_authzServiceMock;

    const SERVICE_METHOD = \Magento\Webapi\Model\Rest\Config::KEY_METHOD;
    const SERVICE_ID = \Magento\Webapi\Model\Rest\Config::KEY_CLASS;

    protected function setUp()
    {
        $this->_requestMock = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Request')
            ->setMethods(array('isSecure', 'getRequestData'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_responseMock = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Response')
            ->setMethods(array('sendResponse', 'getHeaders', 'prepareResponse'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_routerMock = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Router')
            ->setMethods(array('match'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_routeMock = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Router\Route')
            ->setMethods(array('isSecure', 'getServiceMethod', 'getServiceClass'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_serviceMock = $this->getMockBuilder('stdClass')
            ->setMethods(array(self::SERVICE_METHOD))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_appStateMock =  $this->getMockBuilder('Magento\App\State')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_oauthServiceMock = $this->getMockBuilder('Magento\Oauth\Oauth')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_oauthHelperMock = $this->getMockBuilder('Magento\Oauth\Helper\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_authzServiceMock = $this->getMockBuilder('Magento\Authz\Service\AuthorizationV1Interface')
            ->disableOriginalConstructor()
            ->getMock();

        /** Init SUT. */
        $this->_restController = new \Magento\Webapi\Controller\Rest(
            $this->_requestMock,
            $this->_responseMock,
            $this->_routerMock,
            $this->_objectManagerMock,
            $this->_appStateMock,
            $this->_oauthServiceMock,
            $this->_oauthHelperMock,
            $this->_authzServiceMock
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

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_restController);
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_routerMock);
        unset($this->_objectManagerMock);
        unset($this->_oauthServiceMock);
        unset($this->_oauthHelperMock);
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

        $this->_restController->dispatch($this->_requestMock);
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals($expectedMsg, $exceptionArray[0]->getMessage());
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
        $this->_authzServiceMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_restController->dispatch($this->_requestMock);
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
        $this->_authzServiceMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));

        // Override default prepareResponse. It should never be called in this case
        $this->_responseMock->expects($this->never())->method('prepareResponse');

        $this->_restController->dispatch($this->_requestMock);
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals('Operation allowed only in HTTPS', $exceptionArray[0]->getMessage());
        $this->assertEquals(\Magento\Webapi\Exception::HTTP_BAD_REQUEST, $exceptionArray[0]->getHttpCode());
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
        $this->_authzServiceMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));

        // Override default prepareResponse. It should never be called in this case
        $this->_responseMock->expects($this->never())->method('prepareResponse');

        $expectedMsg = 'The method "' . self::SERVICE_METHOD . '" of service "'
            . self::SERVICE_ID . '" must return an array.';

        $this->_restController->dispatch($this->_requestMock);
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals($expectedMsg, $exceptionArray[0]->getMessage());
    }

    public function testAuthorizationFailed()
    {
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));
        $this->_authzServiceMock->expects($this->once())->method('isAllowed')->will($this->returnValue(false));

        $this->_restController->dispatch($this->_requestMock);
        /** Ensure that response contains proper error message. */
        $expectedMsg = 'Not Authorized.';
        $this->assertTrue($this->_responseMock->isException());
        $exceptionArray = $this->_responseMock->getException();
        $this->assertEquals($expectedMsg, $exceptionArray[0]->getMessage());
    }
}
