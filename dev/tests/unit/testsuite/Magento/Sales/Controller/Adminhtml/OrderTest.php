<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Magento Adminhtml Order Controller Test
 */
class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectHelper;

    /**
     * Mock for \Magento\Sales\Model\Order
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_orderMock;

    /**
     * Mock for \Magento\Message
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_messageMock;

    /**
     * Mock for \Magento\Sales\Adminhtml\Controller\Order
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_controllerMock;

    /**
     * setup mocks for all functions
     */
    public function setUp()
    {
        $this->_objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_orderMock = $this->getMockBuilder('\Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->setMethods(array('__wakeup', 'getRealOrderId'))
            ->getMock();

        $this->_messageMock = $this->getMockBuilder('\Magento\Message')
            ->disableOriginalConstructor()
            ->setMethods(array('addError'))
            ->getMock();

        $titleMock = $this->getMock('\Magento\App\Action\Title', array('__wakeup', 'add'), array(), '', false);
        $viewMock = $this->getMockForAbstractClass('\Magento\App\ViewInterface');

        $this->_controllerMock= $this->getMockBuilder('\Magento\Sales\Controller\Adminhtml\Stub\Order')
            ->disableOriginalConstructor()
            ->setMethods(array('__wakeup', '_initOrder', '_initAction', '__', 'renderLayout', '_redirect'))
            ->getMock();
        $this->_controllerMock->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $this->_controllerMock->_title = $titleMock;
        $this->_controllerMock->_view = $viewMock;
        $this->_controllerMock->messageManager = $this->_messageMock;

    }

    /**
     * This function checks if the error is added to session in case of ACTION_FLAG_PRODUCTS_PERMISSION_DENIED
     * is set in Sales Order model
     */
    public function testViewActionWithError()
    {
        $msg = 'You need more permissions to view this item.';
        /** @var \PHPUnit_Framework_MockObject_MockObject $exceptionMock */
        $exceptionMock = $this->getMockBuilder('\Magento\App\Action\Exception')
            ->disableOriginalConstructor()
            ->setMethods(array('getMessage'))
            ->getMock();
        $this->_messageMock->expects($this->once())
            ->method('addError')
            ->with($this->equalTo($msg));
        $this->_controllerMock->expects($this->once())
            ->method('_initOrder')
            ->will($this->returnValue($this->_orderMock));
        $this->_controllerMock->expects($this->once())
            ->method('_initAction')
            ->will($this->throwException(new \Magento\App\Action\Exception($msg)));
        $this->_orderMock->expects($this->never())
            ->method('getRealOrderId');


        $this->_controllerMock->viewAction();
    }

    /**
     * This function checks if the error is added to session in case of ACTION_FLAG_PRODUCTS_PERMISSION_DENIED
     * is not set in Sales Order model
     */
    public function testViewActionWithoutError()
    {
        $this->_messageMock->expects($this->never())
            ->method('addError');
        $this->_orderMock->expects($this->once())
            ->method('getRealOrderId')
            ->will($this->returnValue(1));

        $this->_controllerMock->viewAction();
    }
}
