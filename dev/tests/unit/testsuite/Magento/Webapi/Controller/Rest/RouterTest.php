<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Webapi_Controller_Rest_RouterTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Controller_Rest_Router_Route */
    protected $_routeMock;

    /** @var Magento_Webapi_Controller_Rest_Request */
    protected $_request;

    /** @var Magento_Webapi_Model_Rest_Config */
    protected $_apiConfigMock;

    /** @var Magento_Webapi_Controller_Rest_Router */
    protected $_router;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_apiConfigMock = $this->getMockBuilder('Magento_Webapi_Model_Rest_Config')
            ->disableOriginalConstructor()
            ->getMock();
        $deserializerFactory = $this->getMockBuilder('Magento_Webapi_Controller_Rest_Request_Deserializer_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_routeMock = $this->getMockBuilder('Magento_Webapi_Controller_Rest_Router_Route')
            ->disableOriginalConstructor()
            ->setMethods(array('match'))
            ->getMock();
        $applicationMock = $this->getMockBuilder('Magento_Core_Model_App')
            ->disableOriginalConstructor()
            ->getMock();
        $configMock = $this->getMockBuilder('Magento_Core_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();
        $applicationMock->expects($this->once())->method('getConfig')->will($this->returnValue($configMock));
        $configMock->expects($this->once())->method('getAreaFrontName')->will($this->returnValue('rest'));
        $this->_request = new Magento_Webapi_Controller_Rest_Request(
            $applicationMock,
            $deserializerFactory
        );
        /** Initialize SUT. */
        $this->_router = new Magento_Webapi_Controller_Rest_Router($this->_apiConfigMock);
    }

    protected function tearDown()
    {
        unset($this->_routeMock);
        unset($this->_request);
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
     * @expectedException Magento_Webapi_Exception
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
}
