<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Model;

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
        $this->_model = $objectManagerHelper->getObject('Magento\GiftCardAccount\Model\Observer');
        $this->_event = new \Magento\Object();
        $this->_observer = new \Magento\Event\Observer(array('event' => $this->_event));
    }

    /**
     * @param float $amount
     * @dataProvider addPaymentGiftCardItemDataProvider
     */
    public function testAddPaymentGiftCardItem($amount)
    {
        $salesModel = $this->getMockForAbstractClass('Magento\Payment\Model\Cart\SalesModel\SalesModelInterface');
        $salesModel->expects(
            $this->once()
        )->method(
            'getDataUsingMethod'
        )->with(
            'base_gift_cards_amount'
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
        $this->_model->addPaymentGiftCardItem($this->_observer);
    }

    public function addPaymentGiftCardItemDataProvider()
    {
        return array(array(0.0), array(0.1), array(-0.1));
    }
}
