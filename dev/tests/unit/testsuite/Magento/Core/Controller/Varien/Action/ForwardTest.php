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
     * @var \Magento\Core\Controller\Varien\Action\Forward
     */
    protected $_object = null;

    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Core\Controller\Response\Http
     */
    protected $_response;

    protected function setUp()
    {
        $helperMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(),
            'Magento\Backend\Helper\DataProxy', false);
        $this->_request  = new \Magento\Core\Controller\Request\Http($helperMock);
        $this->_response = new \Magento\Core\Controller\Response\Http(
            $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false)
        );

        $this->_object = new \Magento\Core\Controller\Varien\Action\Forward($this->_request, $this->_response);
    }

    protected function tearDown()
    {
        unset($this->_object);
        unset($this->_request);
        unset($this->_response);
    }

    /**
     * Test that \Magento\Core\Controller\Varien\Action\Forward::dispatch() does not change dispatched flag
     */
    public function testDispatch()
    {
        $this->_request->setDispatched(true);
        $this->assertTrue($this->_request->isDispatched());
        $this->_object->dispatch('any action');
        $this->assertFalse($this->_request->isDispatched());
    }
}
