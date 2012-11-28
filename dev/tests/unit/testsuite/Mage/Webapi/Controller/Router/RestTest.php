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
    /** @var Mage_Webapi_Controller_Router_Route_Rest|PHPUnit_Framework_MockObject_MockObject */
    protected $_routeMock;

    /** @var Mage_Webapi_Controller_Request_Rest */
    protected $_request;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helperMock;

    /** @var Mage_Webapi_Model_Config_Rest */
    protected $_apiConfigMock;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_apiConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Config_Rest')->disableOriginalConstructor()
            ->getMock();
        $interpreterFactory = $this->getMockBuilder('Mage_Webapi_Controller_Request_Rest_Interpreter_Factory')
            ->disableOriginalConstructor()->getMock();
        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();
        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));
        /** Initialize SUT. */
        $this->_routeMock = $this->getMock('Mage_Webapi_Controller_Router_Route_Rest', array('match'),
            array('/test_route/1'));
        $this->_apiConfigMock->expects($this->once())->method('getAllRestRoutes')
            ->will($this->returnValue(array($this->_routeMock)));

        $this->_request = new Mage_Webapi_Controller_Request_Rest($interpreterFactory, $this->_helperMock);
    }

    public function testMatch()
    {
        $this->_routeMock
            ->expects($this->once())
            ->method('match')
            ->with($this->_request)
            ->will($this->returnValue(array()));
        $router = new Mage_Webapi_Controller_Router_Rest($this->_helperMock, $this->_apiConfigMock);

        $matchedRoute = $router->match($this->_request);
        $this->assertEquals($this->_routeMock, $matchedRoute);
    }

    /**
     * @expectedException Mage_Webapi_Exception
     */
    public function testNotMatch()
    {
        $this->_routeMock
            ->expects($this->once())
            ->method('match')
            ->with($this->_request)
            ->will($this->returnValue(false));
        $router = new Mage_Webapi_Controller_Router_Rest($this->_helperMock, $this->_apiConfigMock);

        $router->match($this->_request);
    }
}
