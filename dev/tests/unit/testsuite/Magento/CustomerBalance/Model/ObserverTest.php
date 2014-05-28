<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CustomerBalance\Model\Observer */
    protected $_model;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Framework\Object
     */
    protected $_event;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento\CustomerBalance\Model\Observer');
        $this->_event = new \Magento\Framework\Object();
        $this->_observer = new \Magento\Framework\Event\Observer(array('event' => $this->_event));
    }

    /**
     * @param float $amount
     * @dataProvider addPaymentCustomerBalanceItemDataProvider
     */
    public function testAddPaymentCustomerBalanceItem($amount)
    {
        $salesModel = $this->getMockForAbstractClass('Magento\Payment\Model\Cart\SalesModel\SalesModelInterface');
        $salesModel->expects(
            $this->once()
        )->method(
            'getDataUsingMethod'
        )->with(
            'customer_balance_base_amount'
        )->will(
            $this->returnValue($amount)
        );
        $cart = $this->getMock('Magento\Payment\Model\Cart', array(), array(), '', false);
        $cart->expects($this->once())->method('getSalesModel')->will($this->returnValue($salesModel));
        if (abs($amount) > 0.0001) {
            $cart->expects($this->once())->method('addDiscount')->with(abs($amount));
        } else {
            $cart->expects($this->never())->method('addDiscount');
        }
        $this->_event->setCart($cart);
        $this->_model->addPaymentCustomerBalanceItem($this->_observer);
    }

    public function addPaymentCustomerBalanceItemDataProvider()
    {
        return array(array(0.0), array(0.1), array(-0.1));
    }

    /**
     * @param array $orderData
     * @param integer $baseRewardAmount
     * @param integer $expectedRewardAmount
     *
     * @dataProvider testModifyRewardedAmountOnRefundDataProvider
     * @covers \Magento\CustomerBalance\Model\Observer::modifyRewardedAmountOnRefund
     */
    public function testModifyRewardedAmountOnRefund($orderData, $baseRewardAmount, $expectedRewardAmount)
    {
        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            array(
                'getBaseCustomerBalanceTotalRefunded', 'getBaseTotalRefunded',
                'getBaseTaxRefunded', 'getBaseShippingRefunded', '__wakeup', '__sleep'
            ),
            array(), '', false
        );
        $orderMock->expects($this->any())->method('getBaseCustomerBalanceTotalRefunded')
            ->will($this->returnValue($orderData['base_customer_balance_total_refunded']));
        $orderMock->expects($this->any())->method('getBaseTotalRefunded')
            ->will($this->returnValue($orderData['base_total_refunded']));
        $orderMock->expects($this->any())->method('getBaseTaxRefunded')
            ->will($this->returnValue($orderData['base_tax_refunded']));
        $orderMock->expects($this->any())->method('getBaseShippingRefunded')
            ->will($this->returnValue($orderData['base_shipping_refunded']));

        $creditMemoMock = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            array('getRewardedAmountAfterRefund', 'setRewardedAmountAfterRefund', 'getOrder', '__wakeup', '__sleep'),
            array(), '', false
        );
        $creditMemoMock->expects($this->any())->method('getOrder')
            ->will($this->returnValue($orderMock));
        $creditMemoMock->expects($this->any())->method('getRewardedAmountAfterRefund')
            ->will($this->returnValue($baseRewardAmount));

        $creditMemoMock->expects($this->once())->method('setRewardedAmountAfterRefund')->with($expectedRewardAmount);
        $this->_event->setCreditmemo($creditMemoMock);

        $this->_model->modifyRewardedAmountOnRefund($this->_observer);
    }

    /**
     * @return array
     */
    public function testModifyRewardedAmountOnRefundDataProvider()
    {
        return array(
            array(
                'orderData' => array(
                    'base_customer_balance_total_refunded' => 100,
                    'base_total_refunded' => 40,
                    'base_tax_refunded' => 10,
                    'base_shipping_refunded' => 10
                ),
                'baseRewardAmount' => 5,
                'expectedRewardAmount' => 25
            ),
            array(
                'orderData' => array(
                    'base_customer_balance_total_refunded' => 10,
                    'base_total_refunded' => 40,
                    'base_tax_refunded' => 10,
                    'base_shipping_refunded' => 10
                ),
                'baseRewardAmount' => 10,
                'expectedRewardAmount' => 20
            )
        );
    }
}
