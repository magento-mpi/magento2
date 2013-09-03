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
 * Test class Magento_Core_Controller_Varien_Action_Redirect
 */
class Magento_Core_Controller_Varien_Action_RedirectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Controller_Varien_Action_Redirect
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

        $this->_object = new Magento_Core_Controller_Varien_Action_Redirect($this->_request, $this->_response);
    }

    protected function tearDown()
    {
        unset($this->_object);
        unset($this->_request);
        unset($this->_response);
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
