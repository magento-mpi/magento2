<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Observer;

class AddPaymentRewardItemTest extends \PHPUnit_Framework_TestCase
{
    /** @var ApplyRewardSalesrulePoints */
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
        $this->model = $this->_objectManagerHelper->getObject(
            'Magento\Reward\Model\Observer\AddPaymentRewardItem'
        );
        $this->eventMock = $this->getMock(
            '\Magento\Framework\Event',
            ['getCart', 'getInvoice'],
            [],
            '',
            false
        );
        $this->observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
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
        $cart = $this->getMock('Magento\Payment\Model\Cart', [], [], '', false);
        $cart->expects($this->once())->method('getSalesModel')->will($this->returnValue($salesModel));
        if (abs($amount) > 0.0001) {
            $cart->expects($this->once())->method('addDiscount')->with(abs($amount));
        } else {
            $cart->expects($this->never())->method('addDiscount');
        }
        $this->eventMock->expects($this->once())->method('getCart')->will($this->returnValue($cart));
        $this->model->execute($this->observerMock);
    }

    public function addPaymentRewardItemDataProvider()
    {
        return [[0.0], [0.1], [-0.1]];
    }
}
