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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_routerListMock;

    protected function setUp()
    {
        $this->_routerListMock = $this->getMock('Magento\App\Route\ConfigInterface');
        $infoProcessorMock = $this->getMock('Magento\App\Request\PathInfoProcessorInterface');
        $infoProcessorMock->expects($this->any())->method('process')->will($this->returnArgument(1));
        $this->_request  = new \Magento\App\Request\Http($this->_routerListMock, $infoProcessorMock);
        $this->_response = new \Magento\App\Response\Http();

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
