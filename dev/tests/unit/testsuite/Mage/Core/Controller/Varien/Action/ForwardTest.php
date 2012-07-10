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
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Zend_Controller_Response_Http
     */
    protected $_response;

    protected function setUp()
    {
        $this->_request = new Zend_Controller_Request_Http();

        $this->_response = new Zend_Controller_Response_Http();

        $this->_object = new Mage_Core_Controller_Varien_Action_Forward($this->_request, $this->_response);
    }

    /**
     * Test that Mage_Core_Controller_Varien_Action_Forward::dispatch() does not change dispatched flag
     */
    public function testDispatch()
    {
        $this->assertFalse($this->_object->getRequest()->isDispatched());
        $this->_object->dispatch('any action');
        $this->assertFalse($this->_object->getRequest()->isDispatched());
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf('Zend_Controller_Request_Abstract', $this->_object->getRequest());
    }

    public function testGetResponse()
    {
        $this->assertInstanceOf('Zend_Controller_Response_Abstract', $this->_object->getResponse());
    }
}
