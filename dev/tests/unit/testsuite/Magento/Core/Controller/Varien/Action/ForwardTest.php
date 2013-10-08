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
 * Test class \Magento\Core\Controller\Varien\Action\Forward
 */
namespace Magento\Core\Controller\Varien\Action;

class ForwardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Action\Forward
     */
    protected $_object = null;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\App\Response\Http
     */
    protected $_response;

    protected function setUp()
    {
        $storeManager = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);
        $helperMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(),
            '', false);
        $this->_request  = new \Magento\App\RequestInterface($storeManager, $helperMock, null);
        $this->_response = new \Magento\App\Response\Http(
            $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false)
        );

        $this->_object = new \Magento\App\Action\Forward($this->_request, $this->_response);
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
