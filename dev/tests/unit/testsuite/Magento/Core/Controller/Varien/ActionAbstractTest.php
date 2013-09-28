<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class Magento_Core_Controller_Varien_ActionAbstract
 */
class Magento_Core_Controller_Varien_ActionAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Controller_Varien_ActionAbstract
     */
    protected $_actionAbstract;

    /**
     * @var Magento_Core_Controller_Request_Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var Magento_Core_Controller_Response_Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_response;

    /**
     * Setup before tests
     *
     * Create request, response and forward action (child of ActionAbstract)
     */
    protected function setUp()
    {
        $helperMock = $this->getMock('Magento_Backend_Helper_Data', array(), array(),
            'Magento_Backend_Helper_DataProxy', false);
        $this->_request = $this->getMock(
            'Magento_Core_Controller_Request_Http',
            array('getRequestedRouteName', 'getRequestedControllerName', 'getRequestedActionName'),
            array($helperMock),
            '',
            false
        );
        $this->_response = $this->getMock('Magento_Core_Controller_Response_Http', array(), array(), '', false);
        $this->_response->headersSentThrowsException = false;
        $this->_actionAbstract = new Magento_Core_Controller_Varien_Action_Forward($this->_request, $this->_response);
    }

    /**
     * Test for getRequest method
     *
     * @test
     * @covers Magento_Core_Controller_Varien_ActionAbstract::getRequest
     */
    public function testGetRequest()
    {
        $this->assertEquals($this->_request, $this->_actionAbstract->getRequest());
    }

    /**
     * Test for getResponse method
     *
     * @test
     * @covers Magento_Core_Controller_Varien_ActionAbstract::getResponse
     */
    public function testGetResponse()
    {
        $this->assertEquals($this->_response, $this->_actionAbstract->getResponse());
    }

    /**
     * Test for getResponse med. Checks that response headers are set correctly
     *
     * @test
     * @covers Magento_Core_Controller_Varien_ActionAbstract::getResponse
     */
    public function testResponseHeaders()
    {
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);

        $storeManager = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);
        $helperMock = $this->getMock('Magento_Backend_Helper_Data', array(), array(),
            'Magento_Backend_Helper_DataProxy', false);
        $request = new Magento_Core_Controller_Request_Http($storeManager, $helperMock);
        $response = new Magento_Core_Controller_Response_Http($eventManager);
        $response->headersSentThrowsException = false;
        $action = new Magento_Core_Controller_Varien_Action_Forward($request, $response);

        $headers = array(
            array(
                'name' => 'X-Frame-Options',
                'value' => 'SAMEORIGIN',
                'replace' => false
            )
        );

        $this->assertEquals($headers, $action->getResponse()->getHeaders());
    }

    /**
     * Test for getFullActionName method
     *
     * @test
     * @covers Magento_Core_Controller_Varien_ActionAbstract::getFullActionName
     */
    public function testGetFullActionName()
    {
        $this->_request->expects($this->once())
            ->method('getRequestedRouteName')
            ->will($this->returnValue('adminhtml'));

        $this->_request->expects($this->once())
            ->method('getRequestedControllerName')
            ->will($this->returnValue('index'));

        $this->_request->expects($this->once())
            ->method('getRequestedActionName')
            ->will($this->returnValue('index'));

        $this->assertEquals('adminhtml_index_index', $this->_actionAbstract->getFullActionName());
    }
}
