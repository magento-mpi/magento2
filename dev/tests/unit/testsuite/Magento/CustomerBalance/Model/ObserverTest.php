<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model;

/**
 * Class ObserverTest
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CustomerBalance\Model\Observer */
    protected $model;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $observer;

    /**
     * @var \Magento\Framework\Object
     */
    protected $event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrencyMock;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->priceCurrencyMock = $this->getMockBuilder('Magento\Directory\Model\PriceCurrency')
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManagerHelper->getObject(
            'Magento\CustomerBalance\Model\Observer',
            ['priceCurrency' => $this->priceCurrencyMock]
        );
        $this->event = new \Magento\Framework\Object();
        $this->observer = new \Magento\Framework\Event\Observer(array('event' => $this->event));
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
        $this->event->setCart($cart);
        $this->model->addPaymentCustomerBalanceItem($this->observer);
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
     * @covers       \Magento\CustomerBalance\Model\Observer::modifyRewardedAmountOnRefund
     */
    public function testModifyRewardedAmountOnRefund($orderData, $baseRewardAmount, $expectedRewardAmount)
    {
        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            array(
                'getBaseCustomerBalanceTotalRefunded',
                'getBaseTotalRefunded',
                'getBaseTaxRefunded',
                'getBaseShippingRefunded',
                '__wakeup',
                '__sleep'
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
        $this->event->setCreditmemo($creditMemoMock);

        $this->model->modifyRewardedAmountOnRefund($this->observer);
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

    public function testCreditmemoDataImport()
    {
        $refundAmount = 10;
        $rate = 2;
        $dataInput = [
            'refund_customerbalance_return' => $refundAmount,
            'refund_customerbalance_return_enable' => true,
            'refund_customerbalance' => true,
            'refund_real_customerbalance' => true
        ];

        $observerMock = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $creditmemoMock = $this->getMockBuilder('Magento\Sales\Model\Order\Creditmemo')
            ->disableOriginalConstructor()
            ->setMethods(['getBaseCustomerBalanceReturnMax', 'getOrder'])
            ->getMock();
        $creditmemoMock->expects($this->once())
            ->method('getBaseCustomerBalanceReturnMax')
            ->willReturn($refundAmount);

        $this->priceCurrencyMock->expects($this->at(0))
            ->method('round')
            ->with($refundAmount)
            ->willReturnArgument(0);
        $this->priceCurrencyMock->expects($this->at(1))
            ->method('round')
            ->with($refundAmount * $rate)
            ->willReturnArgument(0);

        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->setMethods(['getBaseToOrderRate'])
            ->getMock();
        $orderMock->expects($this->once())
            ->method('getBaseToOrderRate')
            ->willReturn($rate);

        $creditmemoMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderMock);

        $eventMock = $this->getMockBuilder('Magento\Framework\Event')
            ->disableOriginalConstructor()
            ->setMethods(['getCreditmemo', 'getInput'])
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getCreditmemo')
            ->willReturn($creditmemoMock);
        $eventMock->expects($this->once())
            ->method('getInput')
            ->willReturn($dataInput);
        $observerMock->expects($this->any())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->assertInstanceOf(get_class($this->model), $this->model->creditmemoDataImport($observerMock));
    }
}
