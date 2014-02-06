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
    protected $_model;

    /**
     * @var \Magento\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Object
     */
    protected $_event;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento\Reward\Model\Observer');
        $this->_event = new \Magento\Object();
        $this->_observer = new \Magento\Event\Observer(['event' => $this->_event]);
    }

    /**
     * @param float $amount
     * @dataProvider addPaymentRewardItemDataProvider
     */
    public function testAddPaymentRewardItem($amount)
    {
        $salesModel = $this->getMockForAbstractClass('Magento\Payment\Model\Cart\SalesModel\SalesModelInterface');
        $salesModel->expects($this->once())
            ->method('getDataUsingMethod')
            ->with('base_reward_currency_amount')
            ->will($this->returnValue($amount));
        $cart = $this->getMock('Magento\Payment\Model\Cart', [], [], '', false);
        $cart->expects($this->once())
            ->method('getSalesModel')
            ->will($this->returnValue($salesModel));
        if (abs($amount) > 0.0001) {
            $cart->expects($this->once())
                ->method('addDiscount')
                ->with(abs($amount));
        } else {
            $cart->expects($this->never())
                ->method('addDiscount');
        }
        $this->_event->setCart($cart);
        $this->_model->addPaymentRewardItem($this->_observer);
    }

    public function addPaymentRewardItemDataProvider()
    {
        return [
            [0.0],
            [0.1],
            [-0.1],
        ];
    }
}
