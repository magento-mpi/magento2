<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \Magento\SalesRule\Model\Coupon|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_couponMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_couponMock = $this->getMock('\Magento\SalesRule\Model\Coupon',
            array('__wakeup', 'save', 'load'), array(), '', false);
        $this->_model = $helper->getObject('Magento\SalesRule\Model\Observer', array('coupon' => $this->_couponMock));
    }

    /**
     * @covers \Magento\SalesRule\Model\Observer::salesOrderAfterPlace
     */
    public function testSalesOrderAfterPlaceWithoutDiscount()
    {
        $event = new \Magento\Event;
        $observer = new \Magento\Event\Observer(array('event' => $event));
        /** @var $mockOrder \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject */
        $mockOrder = $this->getMock('Magento\Sales\Model\Order',
            array('__wakeup', 'getDiscountAmount'), array(), '', false);
        $event->setData('order', $mockOrder);
        $mockOrder->expects($this->once())->method('getDiscountAmount')->will($this->returnValue(0));
        $this->assertInstanceOf('Magento\SalesRule\Model\Observer', $this->_model->salesOrderAfterPlace($observer));
    }

    /**
     * @covers \Magento\SalesRule\Model\Observer::salesOrderAfterPlace
     */
    public function testSalesOrderAfterPlaceWithDiscount()
    {
        $event = new \Magento\Event;
        $observer = new \Magento\Event\Observer(array('event' => $event));
        /** @var $mockOrder \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject */
        $mockOrder = $this->getMock('Magento\Sales\Model\Order', array('__wakeup'), array(), '', false);
        $event->setData('order', $mockOrder);
        $mockOrder->addData(array(
            'discount_amount'  => -10,
            'applied_rule_ids' => '',
            'coupon_code'      => 'some_code'
        ));
        $this->_couponMock->expects($this->once())->method('load')->with('some_code', 'code');
        $this->_couponMock->expects($this->once())->method('save');
        $this->_couponMock->addData(array(
            'id'         => 'some_code',
            'times_used' => 1
        ));
        $this->assertInstanceOf('Magento\SalesRule\Model\Observer', $this->_model->salesOrderAfterPlace($observer));
        $this->assertEquals(2, $this->_couponMock->getTimesUsed());
    }
}
