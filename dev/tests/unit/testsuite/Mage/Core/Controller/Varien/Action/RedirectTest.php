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
 * Test class Mage_Core_Controller_Varien_Action_Redirect
 */
class Mage_Core_Controller_Varien_Action_RedirectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Controller_Varien_Action_Redirect
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

        $this->_object = new Mage_Core_Controller_Varien_Action_Redirect($this->_request, $this->_response);
    }

    public function testDispatch()
    {
        $this->_request->setDispatched(true);
        $this->assertTrue($this->_request->isDispatched());
        $this->_object->dispatch('any action');
        $this->assertTrue($this->_request->isDispatched());

        $this->_request->setDispatched(false);
        $this->assertFalse($this->_request->isDispatched());
        $this->_object->dispatch('any action');
        $this->assertFalse($this->_request->isDispatched());
    }
}
