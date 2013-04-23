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
    /** @var Mage_Webapi_Controller_Router_Route_Rest */
    protected $_routeMock;

    /** @var Mage_Webapi_Controller_Request_Rest */
    protected $_request;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helperMock;

    /** @var Mage_Webapi_Model_Config_Rest */
    protected $_apiConfigMock;

    /** @var Mage_Webapi_Controller_Router_Rest */
    protected $_router;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_apiConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Config_Rest')
            ->disableOriginalConstructor()
            ->getMock();
        $interpreterFactory = $this->getMockBuilder('Mage_Webapi_Controller_Request_Rest_Interpreter_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();
        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));
        $this->_routeMock = $this->getMockBuilder('Mage_Webapi_Controller_Router_Route_Rest')
            ->disableOriginalConstructor()
            ->setMethods(array('match', 'getHttpMethod'))
            ->getMock();
        $this->_request = $this->getMock(
            'Mage_Webapi_Controller_Request_Rest',
            array('getHttpMethod'),
            array($interpreterFactory, $this->_helperMock)
        );
        /** Initialize SUT. */
        $this->_router = new Mage_Webapi_Controller_Router_Rest($this->_helperMock, $this->_apiConfigMock);
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
        $httpMethod = 'GET';
        $this->_apiConfigMock->expects($this->once())
            ->method('getAllRestRoutes')
            ->will($this->returnValue(array($this->_routeMock)));
        $this->_routeMock
            ->expects($this->once())
            ->method('match')
            ->with($this->_request)
            ->will($this->returnValue(array()));
        $this->_routeMock->expects($this->once())->method('getHttpMethod')->will($this->returnValue($httpMethod));
        $this->_request->expects($this->once())->method('getHttpMethod')->will($this->returnValue($httpMethod));

        $matchedRoute = $this->_router->match($this->_request);
        $this->assertEquals($this->_routeMock, $matchedRoute);
    }

    /**
     * @expectedException Mage_Webapi_Exception
     */
    public function testNotMatch()
    {
        $this->_apiConfigMock->expects($this->once())
            ->method('getAllRestRoutes')
            ->will($this->returnValue(array($this->_routeMock)));
        $this->_routeMock
            ->expects($this->once())
            ->method('match')
            ->with($this->_request)
            ->will($this->returnValue(false));

        $this->_router->match($this->_request);
    }
}
