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

namespace Magento\Webapi\Controller\Rest;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Rest\Router\Route */
    protected $_routeMock;

    /** @var \Magento\Webapi\Controller\Rest\Request */
    protected $_request;

    /** @var \Magento\Webapi\Model\Rest\Config */
    protected $_apiConfigMock;

    /** @var \Magento\Webapi\Controller\Rest\Router */
    protected $_router;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_apiConfigMock = $this->getMockBuilder('Magento\Webapi\Model\Rest\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $deserializerFactory = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Request\Deserializer\Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_routeMock = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Router\Route')
            ->disableOriginalConstructor()
            ->setMethods(array('match'))
            ->getMock();
        $applicationMock = $this->getMockBuilder('Magento\Core\Model\App')
            ->disableOriginalConstructor()
            ->getMock();
        $configMock = $this->getMockBuilder('Magento\Core\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $applicationMock->expects($this->once())->method('getConfig')->will($this->returnValue($configMock));
        $configMock->expects($this->once())->method('getAreaFrontName')->will($this->returnValue('rest'));
        $this->_request = new \Magento\Webapi\Controller\Rest\Request(
            $applicationMock,
            $deserializerFactory
        );
        /** Initialize SUT. */
        $this->_router = new \Magento\Webapi\Controller\Rest\Router($this->_apiConfigMock);
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
     * @expectedException \Magento\Webapi\Exception
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
