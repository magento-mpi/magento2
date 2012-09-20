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

    protected $_request;

    protected function setUp()
    {
        $this->_request = new Mage_Webapi_Model_Request();
        $this->_routeMock = $this->getMock('Mage_Webapi_Controller_Router_Route_Rest', array('match'),
            array('/test_route/1'));
    }

    public function testRoutesAccessor()
    {
        $router = new Mage_Webapi_Controller_Router_Rest();
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
        $router = new Mage_Webapi_Controller_Router_Rest();
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
        $router = new Mage_Webapi_Controller_Router_Rest();
        $router->setRoutes($routes);

        $router->match($this->_request);
    }
}
