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
 * Test class Mage_Core_Controller_Varien_Action_Forward
 */
class Mage_Core_Controller_Varien_Action_ForwardTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Controller_Varien_Action_Forward
     */
    protected $_object = null;

    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Mage_Core_Controller_Response_Http
     */
    protected $_response;

    protected function setUp()
    {
        $this->_request  = new Mage_Core_Controller_Request_Http();
        $this->_response = new Mage_Core_Controller_Response_Http();

        $this->_object = new Mage_Core_Controller_Varien_Action_Forward($this->_request, $this->_response);
    }

    protected function tearDown()
    {
        unset($this->_object);
        unset($this->_request);
        unset($this->_response);
    }

    /**
     * Test that Mage_Core_Controller_Varien_Action_Forward::dispatch() does not change dispatched flag
     */
    public function testDispatch()
    {
        $this->_request->setDispatched(true);
        $this->assertTrue($this->_request->isDispatched());
        $this->_object->dispatch('any action');
        $this->assertFalse($this->_request->isDispatched());
    }
}
