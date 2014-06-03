<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var Observer */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $this->_objectManagerHelper->getObject('Magento\Reward\Model\Observer');
        $this->eventMock = $this->getMock(
            '\Magento\Framework\Event',
            array('getCart', 'getInvoice'),
            array(),
            '',
            false
        );
        $this->observerMock = $this->getMock('\Magento\Framework\Event\Observer', array(), array(), '', false);
        $this->observerMock->expects($this->any())->method('getEvent')->will($this->returnValue($this->eventMock));
    }

    /**
     * @param float $amount
     * @dataProvider addPaymentRewardItemDataProvider
     */
    public function testAddPaymentRewardItem($amount)
    {
        $salesModel = $this->getMockForAbstractClass('Magento\Payment\Model\Cart\SalesModel\SalesModelInterface');
        $salesModel->expects(
            $this->once()
        )->method(
            'getDataUsingMethod'
        )->with(
            'base_reward_currency_amount'
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
        $this->eventMock->expects($this->once())->method('getCart')->will($this->returnValue($cart));
        $this->model->addPaymentRewardItem($this->observerMock);
    }

    public function addPaymentRewardItemDataProvider()
    {
        return array(array(0.0), array(0.1), array(-0.1));
    }

    public function testApplyRewardSalesrulePoints()
    {
        $invoiceMock = $this->getMock('\Magento\Sales\Model\Order\Invoice', array(), array(), '', false);
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', array(), array(), '', false);

        $this->eventMock->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $invoiceMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $invoiceMock->expects($this->once())->method('hasDataChanges')->will($this->returnValue(true));
        $orderMock->expects($this->never())->method('getCustomerId');

        $this->model->applyRewardSalesrulePoints($this->observerMock);
    }
}
