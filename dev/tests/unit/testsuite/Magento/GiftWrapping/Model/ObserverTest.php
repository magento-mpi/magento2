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
        $this->_model = $objectManagerHelper->getObject('Magento\GiftWrapping\Model\Observer');
        $this->_event = new \Magento\Framework\Object();
        $this->_observer = new \Magento\Framework\Event\Observer(array('event' => $this->_event));
    }

    public function testCheckoutProcessWrappingInfoQuote()
    {
        $giftWrappingInfo = ['quote' => [1 => ['some data']]];
        $requestMock = $this->getMock('\Magento\Framework\App\RequestInterface', [], [], '', false);
        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $event = new \Magento\Framework\Event(['request' => $requestMock, 'quote' => $quoteMock]);
        $observer = new \Magento\Framework\Object(['event' => $event]);

        $requestMock->expects($this->once())
            ->method('getParam')
            ->with('giftwrapping')
            ->will($this->returnValue($giftWrappingInfo));

        $quoteMock->expects($this->once())->method('getShippingAddress')->will($this->returnValue(false));
        $quoteMock->expects($this->once())->method('addData')->will($this->returnSelf());
        $quoteMock->expects($this->never())->method('getAddressById');
        $this->_model->checkoutProcessWrappingInfo($observer);
    }

    public function testCheckoutProcessWrappingInfoQuoteItem()
    {
        $giftWrappingInfo = ['quote_item' => [1 => ['some data']]];
        $requestMock = $this->getMock('\Magento\Framework\App\RequestInterface', [], [], '', false);
        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $event = new \Magento\Framework\Event(['request' => $requestMock, 'quote' => $quoteMock]);
        $observer = new \Magento\Framework\Object(['event' => $event]);

        $requestMock->expects($this->once())
            ->method('getParam')
            ->with('giftwrapping')
            ->will($this->returnValue($giftWrappingInfo));

        $quoteMock->expects($this->once())->method('getItemById')->will($this->returnSelf());
        $this->_model->checkoutProcessWrappingInfo($observer);
    }

    public function testCheckoutProcessWrappingInfoQuoteAddress()
    {
        $giftWrappingInfo = ['quote_address' => [1 => ['some data']]];
        $requestMock = $this->getMock('\Magento\Framework\App\RequestInterface', [], [], '', false);
        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $event = new \Magento\Framework\Event(['request' => $requestMock, 'quote' => $quoteMock]);
        $observer = new \Magento\Framework\Object(['event' => $event]);

        $requestMock->expects($this->once())
            ->method('getParam')
            ->with('giftwrapping')
            ->will($this->returnValue($giftWrappingInfo));

        $quoteMock->expects($this->once())->method('getAddressById')->will($this->returnSelf());
        $quoteMock->expects($this->once())->method('getShippingAddress')->will($this->returnValue(false));
        $quoteMock->expects($this->once())->method('addData')->will($this->returnSelf());
        $this->_model->checkoutProcessWrappingInfo($observer);
    }

    public function testCheckoutProcessWrappingInfoQuoteAddressItem()
    {
        $giftWrappingInfo = ['quote_address_item' => [1 => ['some data']]];
        $requestMock = $this->getMock('\Magento\Framework\App\RequestInterface', [], [], '', false);
        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $event = new \Magento\Framework\Event(['request' => $requestMock, 'quote' => $quoteMock]);
        $observer = new \Magento\Framework\Object(['event' => $event]);

        $requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('giftwrapping')
            ->will($this->returnValue($giftWrappingInfo));

        $requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('giftoptions')
            ->will($this->returnValue(['quote_address_item' => [1 => ['address' => 'some address']]]));

        $quoteMock->expects($this->once())->method('getAddressById')->will($this->returnSelf());
        $quoteMock->expects($this->once())->method('getItemById')->will($this->returnSelf());
        $this->_model->checkoutProcessWrappingInfo($observer);
    }

    public function testCheckoutProcessWrappingInfoEmpty()
    {
        $requestMock = $this->getMock('\Magento\Framework\App\RequestInterface', [], [], '', false);
        $event = new \Magento\Framework\Event(['request' => $requestMock]);
        $observerMock = $this->getMock('\Magento\Framework\Object', ['getEvent'], [], '', false);
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $requestMock->expects($this->once())
            ->method('getParam')
            ->with('giftwrapping')
            ->will($this->returnValue(false));
        $this->assertEquals($this->_model, $this->_model->checkoutProcessWrappingInfo($observerMock));
    }

    /**
     * @dataProvider checkoutProcessWrappingInfoWrongDataProvider
     * @param $giftWrappingInfo
     * @param $expectedMessage
     */
    public function testCheckoutProcessWrappingInfoWrongData($giftWrappingInfo, $expectedMessage)
    {
        $requestMock = $this->getMock('\Magento\Framework\App\RequestInterface', [], [], '', false);
        $event = new \Magento\Framework\Event(['request' => $requestMock]);
        $observer = new \Magento\Framework\Object(['event' => $event]);

        $requestMock->expects($this->once())
            ->method('getParam')
            ->with('giftwrapping')
            ->will($this->returnValue($giftWrappingInfo));
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);
        $this->_model->checkoutProcessWrappingInfo($observer);
    }

    public function checkoutProcessWrappingInfoWrongDataProvider()
    {
        return [
            'empty type' => [['empty_type' => false], 'Invalid entity by index empty_type'],
            'invalid type' => [['invalid_type' => ['a' => 'b']], 'Invalid wrapping type:invalid_type'],
        ];
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
                new \Magento\Framework\Object(
                    array('parent_item' => 'something', 'gw_id' => 1, 'gw_base_price' => 0.3)
                ),
                new \Magento\Framework\Object(array('gw_id' => null, 'gw_base_price' => 0.3)),
                new \Magento\Framework\Object(array('gw_id' => 1, 'gw_base_price' => 0.0)),
                new \Magento\Framework\Object(array('gw_id' => 2, 'gw_base_price' => null)),
                new \Magento\Framework\Object(array('gw_id' => 3, 'gw_base_price' => 0.12)),
                new \Magento\Framework\Object(array('gw_id' => 4, 'gw_base_price' => 2.1))
            )
        );
        $itemsPrice = array(0, 0.12 + 2.1);
        $data = array();
        foreach ($amounts as $amount) {
            foreach ($originalItems as $i => $originalItemsSet) {
                $items = array();
                foreach ($originalItemsSet as $originalItem) {
                    $items[] = new \Magento\Framework\Object(array('original_item' => $originalItem));
                }
                $data[] = array($items, $amount, $itemsPrice[$i] + (double)$amount);
            }
        }
        return $data;
    }
}
