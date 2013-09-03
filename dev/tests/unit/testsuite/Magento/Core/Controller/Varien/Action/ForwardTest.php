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
 * Test class Magento_Core_Controller_Varien_Action_Forward
 */
class Magento_Core_Controller_Varien_Action_ForwardTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Controller_Varien_Action_Forward
     */
    protected $_object = null;

    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Magento_Core_Controller_Response_Http
     */
    protected $_response;

    protected function setUp()
    {
        $this->_request  = new Magento_Core_Controller_Request_Http($this->getMock('Magento_Backend_Helper_Data',
            array(), array(), '', false));
        $this->_response = new Magento_Core_Controller_Response_Http();

        $this->_object = new Magento_Core_Controller_Varien_Action_Forward($this->_request, $this->_response);
    }

    protected function tearDown()
    {
        unset($this->_object);
        unset($this->_request);
        unset($this->_response);
    }

    /**
     * Test that Magento_Core_Controller_Varien_Action_Forward::dispatch() does not change dispatched flag
     */
    public function testDispatch()
    {
        $this->_request->setDispatched(true);
        $this->assertTrue($this->_request->isDispatched());
        $this->_object->dispatch('any action');
        $this->assertFalse($this->_request->isDispatched());
    }
}
