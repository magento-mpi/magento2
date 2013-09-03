<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Controller_Router_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Rest_Router_Route */
    protected $_routeMock;

    /** @var Mage_Webapi_Controller_Rest_Request */
    protected $_request;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helperMock;

    /** @var Mage_Webapi_Model_Rest_Config */
    protected $_apiConfigMock;

    /** @var Mage_Webapi_Controller_Rest_Router */
    protected $_router;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_apiConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Rest_Config')
            ->disableOriginalConstructor()
            ->getMock();
        $interpreterFactory = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Request_Interpreter_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();
        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));
        $this->_routeMock = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Router_Route')
            ->disableOriginalConstructor()
            ->setMethods(array('match'))
            ->getMock();
        $this->_request = new Mage_Webapi_Controller_Rest_Request($interpreterFactory, $this->_helperMock);
        /** Initialize SUT. */
        $this->_router = new Mage_Webapi_Controller_Rest_Router($this->_helperMock, $this->_apiConfigMock);
    }

    protected function tearDown()
    {
        unset($this->_routeMock);
        unset($this->_request);
        unset($this->_helperMock);
        unset($this->_apiConfigMock);
        unset($this->_router);
        parent::tearDown();
    }

    public function testMatch()
    {
        $this->_apiConfigMock->expects($this->once())
            ->method('getRestRoutes')
            ->will($this->returnValue(array($this->_routeMock)));
        $this->_routeMock->expects($this->once())
            ->method('match')
            ->with($this->_request)
            ->will($this->returnValue(array()));

        $matchedRoute = $this->_router->match($this->_request);
        $this->assertEquals($this->_routeMock, $matchedRoute);
    }

    /**
     * @expectedException Mage_Webapi_Exception
     */
    public function testNotMatch()
    {
        $this->_apiConfigMock->expects($this->once())
            ->method('getRestRoutes')
            ->will($this->returnValue(array($this->_routeMock)));
        $this->_routeMock
            ->expects($this->once())
            ->method('match')
            ->with($this->_request)
            ->will($this->returnValue(false));

        $this->_router->match($this->_request);
    }

    /**
     * Prepare mocks for SUT constructor for testCheckRoute().
     *
     * @return array
     */
    protected function _prepareMockDataForCheckRouteTest()
    {
        $methodName = 'foo';
        $version = 'bar';
        $request = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Request')
            ->disableOriginalConstructor()
            ->setMethods(array('getServiceName'))
            ->getMock();
        $serviceName = 'Service Name';
        $request->expects($this->once())
            ->method('getServiceName')
            ->will($this->returnValue($serviceName));
        $this->_apiConfigMock->expects($this->once())
            ->method('getMethodRestRoutes')
            ->with($serviceName, $methodName, $version)
            ->will($this->returnValue(array($this->_routeMock)));
        return array('request' => $request, 'methodName' => $methodName, 'version' => $version);
    }
}
