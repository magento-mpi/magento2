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

    /** @var \Magento\Framework\App\State */
    protected $_appStateMock;

    /** @var \Magento\Authz\Service\AuthorizationV1Interface */
    protected $_authzServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $areaListMock;

    /**
     * @var \Magento\Webapi\Controller\ServiceArgsSerializer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $areaMock;

    const SERVICE_METHOD = 'testMethod';

    const SERVICE_ID = 'Magento\Webapi\Controller\TestService';

    protected function mockArguments()
    {
        $this->_requestMock = $this->getMockBuilder(
            'Magento\Webapi\Controller\Rest\Request'
        )->setMethods(
            array('isSecure', 'getRequestData')
        )->disableOriginalConstructor()->getMock();

        $this->_responseMock = $this->getMockBuilder(
            'Magento\Webapi\Controller\Rest\Response'
        )->setMethods(
            array('sendResponse', 'getHeaders', 'prepareResponse')
        )->disableOriginalConstructor()->getMock();

        $this->_routerMock = $this->getMockBuilder(
            'Magento\Webapi\Controller\Rest\Router'
        )->setMethods(
            array('match')
        )->disableOriginalConstructor()->getMock();

        $this->_routeMock = $this->getMockBuilder(
            'Magento\Webapi\Controller\Rest\Router\Route'
        )->setMethods(
            array('isSecure', 'getServiceMethod', 'getServiceClass', 'getParameters')
        )->disableOriginalConstructor()->getMock();

        $this->_objectManagerMock = $this->getMockBuilder(
            'Magento\ObjectManager'
        )->disableOriginalConstructor()->getMock();

        $this->_serviceMock = $this->getMockBuilder(
            self::SERVICE_ID
        )->setMethods(
            array(self::SERVICE_METHOD)
        )->disableOriginalConstructor()->getMock();

        $this->_appStateMock = $this->getMockBuilder('Magento\Framework\App\State')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_authzServiceMock = $this->getMockBuilder(
            'Magento\Authz\Service\AuthorizationV1Interface'
        )->disableOriginalConstructor()->getMock();
    }

    protected function setUp()
    {
        $this->mockArguments();

        $layoutMock = $this->getMockBuilder('Magento\Framework\View\LayoutInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $errorProcessorMock = $this->getMock('Magento\Webapi\Controller\ErrorProcessor', array(), array(), '', false);
        $errorProcessorMock->expects($this->any())->method('maskException')->will($this->returnArgument(0));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->serializerMock = $this->getMockBuilder('\Magento\Webapi\Controller\ServiceArgsSerializer')
            ->disableOriginalConstructor()
            ->setMethods(['getInputData'])->getMock();
        $this->areaListMock = $this->getMock('\Magento\Framework\App\AreaList', array(), array(), '', false);
        $this->areaMock = $this->getMock('Magento\Framework\App\AreaInterface');
        $this->areaListMock->expects($this->any())->method('getArea')->will($this->returnValue($this->areaMock));

        /** Init SUT. */
        $this->_restController = $objectManager->getObject(
            'Magento\Webapi\Controller\Rest',
            array(
                'request' => $this->_requestMock,
                'response' => $this->_responseMock,
                'router' => $this->_routerMock,
                'objectManager' => $this->_objectManagerMock,
                'appState' => $this->_appStateMock,
                'layout' => $layoutMock,
                'authorizationService' => $this->_authzServiceMock,
                'serializer' => $this->serializerMock,
                'errorProcessor' => $errorProcessorMock,
                'areaList' => $this->areaListMock
            )
        );

        // Set default expectations used by all tests
        $this->_routeMock->expects(
            $this->any()
        )->method(
            'getServiceClass'
        )->will(
            $this->returnValue(self::SERVICE_ID)
        );

        $this->_routeMock->expects(
            $this->any()
        )->method(
            'getServiceMethod'
        )->will(
            $this->returnValue(self::SERVICE_METHOD)
        );
        $this->_routerMock->expects($this->any())->method('match')->will($this->returnValue($this->_routeMock));

        $this->_objectManagerMock->expects($this->any())->method('get')->will($this->returnValue($this->_serviceMock));
        $this->_responseMock->expects($this->any())->method('prepareResponse')->will($this->returnValue(array()));
        $this->_serviceMock->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue(null));

        parent::setUp();
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
        $this->_serviceMock->expects($this->any())->method(self::SERVICE_METHOD)->will($this->returnValue(array()));
        $this->_routeMock->expects($this->any())->method('isSecure')->will($this->returnValue($isSecureRoute));
        $this->_routeMock->expects($this->once())->method('getParameters')->will($this->returnValue(array()));
        $this->_requestMock->expects($this->any())->method('getRequestData')->will($this->returnValue(array()));
        $this->_requestMock->expects($this->any())->method('isSecure')->will($this->returnValue($isSecureRequest));
        $this->_authzServiceMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->serializerMock->expects($this->any())->method('getInputData')->will($this->returnValue([]));
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
        // Each array contains return type for isSecure method of route and request objects.
        return array(array(true, true), array(false, true), array(false, false));
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

    /**
     * @param array $requestData Data from the request
     * @param array $parameters Data from config about which parameters to override
     * @param array $expectedOverriddenParams Result of overriding $requestData when applying rules from $parameters
     *
     * @dataProvider overrideParmasDataProvider
     */
    public function testOverrideParams($requestData, $parameters, $expectedOverriddenParams)
    {
        $this->_routeMock->expects($this->once())->method('getParameters')->will($this->returnValue($parameters));
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));
        $this->_authzServiceMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_requestMock->expects($this->any())->method('getRequestData')->will($this->returnValue($requestData));

        // serializer should expect overridden params
        $this->serializerMock->expects($this->once())->method('getInputData')
            ->with(
                $this->equalTo('Magento\Webapi\Controller\TestService'),
                $this->equalTo('testMethod'),
                $this->equalTo($expectedOverriddenParams)
            );

        $this->_restController->dispatch($this->_requestMock);
    }

    /**
     * @return array
     */
    public function overrideParmasDataProvider()
    {
        return [
            'force false, value present' => [
                ['Name1' => 'valueIn'],
                ['Name1' => ['force' => false, 'value' => 'valueOverride']],
                ['Name1' => 'valueIn'],
            ],
            'force true, value present' => [
                ['Name1' => 'valueIn'],
                ['Name1' => ['force' => true, 'value' => 'valueOverride']],
                ['Name1' => 'valueOverride']
            ],
            'force true, value not present' => [
                ['Name1' => 'valueIn'],
                ['Name2' => ['force' => true, 'value' => 'valueOverride']],
                ['Name1' => 'valueIn', 'Name2' => 'valueOverride']
            ],
            'force false, value not present' => [
                ['Name1' => 'valueIn'],
                ['Name2' => ['force' => false, 'value' => 'valueOverride']],
                ['Name1' => 'valueIn', 'Name2' => 'valueOverride'],
            ],
        ];
    }
}

class TestService
{
    public function testMethod()
    {
        return null;
    }
}
