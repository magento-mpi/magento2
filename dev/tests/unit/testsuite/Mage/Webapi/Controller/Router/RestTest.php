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

    /** @var Mage_Core_Model_Factory_Helper */
    protected $_helperFactory;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $interpreterFactory = $this->getMockBuilder('Mage_Webapi_Controller_Request_Rest_Interpreter_Factory')
            ->disableOriginalConstructor()->getMock();
        $helper = $this->getMock('Mage_Webapi_Helper_Data', array('__'));
        $helper->expects($this->any())->method('__')->will($this->returnArgument(0));
        $this->_helperFactory = $this->getMock('Mage_Core_Model_Factory_Helper');
        $this->_helperFactory->expects($this->any())->method('get')->will($this->returnValue($helper));
        /** Initialize SUT. */
        // TODO: Get rid of SUT mocks.
        $this->_routeMock = $this->getMock('Mage_Webapi_Controller_Router_Route_Rest', array('match'),
            array('/test_route/1'));

        $this->_request = new Mage_Webapi_Controller_Request_Rest($interpreterFactory, $this->_helperFactory);
    }

    public function testRoutesAccessor()
    {
        $router = new Mage_Webapi_Controller_Router_Rest($this->_helperFactory);
        $routes = array($this->_routeMock);
        $this->assertInstanceOf('Mage_Webapi_Controller_Router_Rest', $router->setRoutes($routes));
        $this->assertEquals($routes, $router->getRoutes());
    }

    public function testMatch()
    {
        $this->_routeMock
            ->expects($this->once())
            ->method('match')
            ->with($this->_request)
            ->will($this->returnValue(array()));
        $routes = array($this->_routeMock);
        $router = new Mage_Webapi_Controller_Router_Rest($this->_helperFactory);
        $router->setRoutes($routes);

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
        $routes = array($this->_routeMock);
        $router = new Mage_Webapi_Controller_Router_Rest($this->_helperFactory);
        $router->setRoutes($routes);

        $router->match($this->_request);
    }
}
