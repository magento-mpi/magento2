<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class Mage_Core_Controller_Varien_ActionAbstract
 */
class Mage_Core_Controller_Varien_ActionAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Controller_Varien_ActionAbstract
     */
    protected $_actionAbstract;

    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Mage_Core_Controller_Response_Http
     */
    protected $_response;

    public function setUp()
    {
        $this->_request = $this->getMock('Mage_Core_Controller_Request_Http',
            array('getRequestedRouteName', 'getRequestedControllerName', 'getRequestedActionName'), array(), '', false
        );
        $this->_response = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false);
        $this->_actionAbstract = new Mage_Core_Controller_Varien_Action_Forward($this->_request, $this->_response,
            'Area'
        );
    }

    public function testConstruct()
    {
        $this->assertAttributeInstanceOf('Mage_Core_Controller_Request_Http', '_request', $this->_actionAbstract);
        $this->assertAttributeInstanceOf('Mage_Core_Controller_Response_Http', '_response', $this->_actionAbstract);
        $this->assertAttributeEquals('Area', '_currentArea', $this->_actionAbstract);
    }

    public function testGetRequest()
    {
        $this->assertEquals($this->_request, $this->_actionAbstract->getRequest());
    }

    public function testGetResponse()
    {
        $this->assertEquals($this->_response, $this->_actionAbstract->getResponse());
    }

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
