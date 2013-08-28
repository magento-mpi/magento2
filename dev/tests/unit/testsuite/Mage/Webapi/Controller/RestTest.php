<?php
/**
 * Test Rest controller dispatcher.
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

    /** @var Mage_Webapi_Controller_Rest_Presentation */
    protected $_restPresentation;

    /** @var Magento_ObjectManager */
    protected $_objectManagerMock;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helperMock;

    protected function setUp()
    {
        $this->markTestIncomplete(
            "This unit test has an incorrect mocking and needs to be fixed"
        );

        /** Init dependencies for SUT. */
        $this->_requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Request')->disableOriginalConstructor()
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Response')
            ->disableOriginalConstructor()->getMock();
        $this->_restPresentation = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Presentation')
            ->disableOriginalConstructor()->getMock();
        $this->_routerMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Router')->disableOriginalConstructor()
            ->getMock();
        $this->_authenticationMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Authentication')
            ->disableOriginalConstructor()->getMock();
        $this->_objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()->getMock();
        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')->disableOriginalConstructor()->getMock();
        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));

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
        //$this->_authenticationMock->expects($this->once())->method('authenticate');
        /** Init route mock. */
        $routeMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Router_Route')->disableOriginalConstructor()
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
        $serviceId = 'SomeService';
        $serviceMethodName = 'someMethod';
        //$this->_authenticationMock->expects($this->once())->method('authenticate');
        /** Init route mock. */
        $routeMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Router_Route')->disableOriginalConstructor()
            ->getMock();

        $serviceMock = $this->getMock('StdClass', array($serviceMethodName));

        $routeMock->expects($this->any())->method('getServiceId')->will($this->returnValue($serviceId));
        $routeMock->expects($this->any())->method('getServiceMethod')->will($this->returnValue($serviceMethodName));
        $routeMock->expects($this->any())->method('getServiceVersion');
        $routeMock->expects($this->any())->method('isSecure')->will($this->returnValue(true));

        $this->_objectManagerMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(array($serviceId, $serviceMock))));

        $this->_routerMock->expects($this->once())->method('match')->will($this->returnValue($routeMock));
        $this->_restPresentation->expects($this->once())->method('prepareResponse')->will(
            $this->returnValue(array())
        );
        $this->_requestMock->expects($this->any())->method('isSecure')->will($this->returnValue(true));
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
        $routeMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Router_Route')->disableOriginalConstructor()
            ->getMock();
        $routeMock->expects($this->any())->method('getServiceId');
        $routeMock->expects($this->any())->method('getServiceMethod');
        $routeMock->expects($this->any())->method('getServiceVersion');
        $routeMock->expects($this->any())->method('isSecure')->will($this->returnValue(true));
        $this->_routerMock->expects($this->once())->method('match')->will($this->returnValue($routeMock));

        /** Assert that setException method will be executed once. */
        $this->_responseMock->expects($this->once())->method('setException')->with(
            new Mage_Webapi_Exception('Operation allowed only in HTTPS', Mage_Webapi_Exception::HTTP_FORBIDDEN)
        );

        $this->_restDispatcher->dispatch();
    }
}
