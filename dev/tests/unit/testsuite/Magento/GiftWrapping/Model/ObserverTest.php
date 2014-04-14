<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model;

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
        $this->_model = $objectManagerHelper->getObject('Magento\GiftWrapping\Model\Observer');
        $this->_event = new \Magento\Object();
        $this->_observer = new \Magento\Event\Observer(array('event' => $this->_event));
    }

    /**
     * @param float $amount
     * @dataProvider addPaymentGiftWrappingItemTotalCardDataProvider
     */
    public function testAddPaymentGiftWrappingItemTotalCard($amount)
    {
        $salesModel = $this->getMockForAbstractClass('Magento\Payment\Model\Cart\SalesModel\SalesModelInterface');
        $salesModel->expects($this->once())->method('getAllItems')->will($this->returnValue(array()));
        $salesModel->expects($this->any())->method('getDataUsingMethod')->will(
            $this->returnCallback(
                function ($key) use ($amount) {
                    if ($key == 'gw_card_base_price') {
                        return $amount;
                    } elseif ($key == 'gw_add_card' && is_float($amount)) {
                        return true;
                    } else {
                        return null;
                    }
                }
            )
        );
        $cart = $this->getMock('Magento\Payment\Model\Cart', array(), array(), '', false);
        $cart->expects($this->once())->method('getSalesModel')->will($this->returnValue($salesModel));
        if ($amount) {
            $cart->expects($this->once())->method('addCustomItem')->with(__('Printed Card'), 1, $amount);
        } else {
            $cart->expects($this->never())->method('addCustomItem');
        }
        $this->_event->setCart($cart);
        $this->_model->addPaymentGiftWrappingItem($this->_observer);
    }

    public function addPaymentGiftWrappingItemTotalCardDataProvider()
    {
        return array(array(null), array(0), array(0.12));
    }

    /**
     * @param array $items
     * @param float $amount
     * @param float $expected
     * @dataProvider addPaymentGiftWrappingItemTotalWrappingDataProvider
     */
    public function testAddPaymentGiftWrappingItemTotalWrapping(array $items, $amount, $expected)
    {
        $salesModel = $this->getMockForAbstractClass('Magento\Payment\Model\Cart\SalesModel\SalesModelInterface');
        $salesModel->expects($this->once())->method('getAllItems')->will($this->returnValue($items));
        $salesModel->expects($this->any())->method('getDataUsingMethod')->will(
            $this->returnCallback(
                function ($key) use ($amount) {
                    if ($key == 'gw_base_price') {
                        return $amount;
                    } elseif ($key == 'gw_id' && is_float($amount)) {
                        return 1;
                    } else {
                        return null;
                    }
                }
            )
        );
        $cart = $this->getMock('Magento\Payment\Model\Cart', array(), array(), '', false);
        $cart->expects($this->once())->method('getSalesModel')->will($this->returnValue($salesModel));
        if ($expected) {
            $cart->expects($this->once())->method('addCustomItem')->with(__('Gift Wrapping'), 1, $expected);
        } else {
            $cart->expects($this->never())->method('addCustomItem');
        }
        $this->_event->setCart($cart);
        $this->_model->addPaymentGiftWrappingItem($this->_observer);
    }

    public function addPaymentGiftWrappingItemTotalWrappingDataProvider()
    {
        $amounts = array(null, 0, 0.12);
        $originalItems = array(
            array(),
            array(
                new \Magento\Object(array('parent_item' => 'something', 'gw_id' => 1, 'gw_base_price' => 0.3)),
                new \Magento\Object(array('gw_id' => null, 'gw_base_price' => 0.3)),
                new \Magento\Object(array('gw_id' => 1, 'gw_base_price' => 0.0)),
                new \Magento\Object(array('gw_id' => 2, 'gw_base_price' => null)),
                new \Magento\Object(array('gw_id' => 3, 'gw_base_price' => 0.12)),
                new \Magento\Object(array('gw_id' => 4, 'gw_base_price' => 2.1))
            )
        );
        $itemsPrice = array(0, 0.12 + 2.1);
        $data = array();
        foreach ($amounts as $amount) {
            foreach ($originalItems as $i => $originalItemsSet) {
                $items = array();
                foreach ($originalItemsSet as $originalItem) {
                    $items[] = new \Magento\Object(array('original_item' => $originalItem));
                }
                $data[] = array($items, $amount, $itemsPrice[$i] + (double)$amount);
            }
        }
        return $data;
    }
}
