<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Order;

class InvoiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Order\Invoice
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Order
     */
    protected $_orderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Order\Payment
     */
    protected $_paymentMock;

    protected function setUp()
    {
        $helperManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->setMethods(array('getPayment'))
            ->getMock();
        $this->_paymentMock = $this->getMockBuilder('Magento\Sales\Model\Order\Payment')
            ->disableOriginalConstructor()
            ->setMethods(array('canVoid'))
            ->getMock();

        $this->_model = $helperManager->getObject('Magento\Sales\Model\Order\Invoice', array());
        $this->_model->setOrder($this->_orderMock);
    }

    /**
     * @dataProvider canVoidDataProvider
     * @param bool $canVoid
     */
    public function testCanVoid($canVoid)
    {
        $this->_orderMock->expects($this->once())->method('getPayment')->will($this->returnValue($this->_paymentMock));
        $this->_paymentMock->expects($this->once())
            ->method('canVoid')
            ->with($this->equalTo($this->_model))
            ->will($this->returnValue($canVoid));

        $this->_model->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
        $this->assertEquals($canVoid, $this->_model->canVoid());
    }

    /**
     * @dataProvider canVoidDataProvider
     * @param bool $canVoid
     */
    public function testDefaultCanVoid($canVoid)
    {
        $this->_model->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
        $this->_model->setCanVoidFlag($canVoid);

        $this->assertEquals($canVoid, $this->_model->canVoid());
    }

    public function canVoidDataProvider()
    {
        return array(
            array(true),
            array(false)
        );
    }
}
