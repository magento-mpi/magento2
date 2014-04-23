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
        $this->_model = $objectManagerHelper->getObject('Magento\Reward\Model\Observer');
        $this->_event = new \Magento\Framework\Object();
        $this->_observer = new \Magento\Framework\Event\Observer(array('event' => $this->_event));
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
        $this->_event->setCart($cart);
        $this->_model->addPaymentRewardItem($this->_observer);
    }

    public function addPaymentRewardItemDataProvider()
    {
        return array(array(0.0), array(0.1), array(-0.1));
    }
}
