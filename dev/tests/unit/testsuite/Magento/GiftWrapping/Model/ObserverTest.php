<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventMock;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->helperDataMock = $this->getMock('Magento\GiftWrapping\Helper\Data', [], [], '', false);
        $this->observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->eventMock = $this->getMock('Magento\Framework\Event',
            [
                'getQuote',
                'getItems',
                'getOrder',
                'getOrderItem',
                'getQuoteItem',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->_model = $objectManagerHelper->getObject('Magento\GiftWrapping\Model\Observer',
            [
               'giftWrappingData' =>  $this->helperDataMock
            ]);
        $this->_event = new \Magento\Framework\Object();
        $this->_observer = new \Magento\Framework\Event\Observer(['event' => $this->_event]);
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

    public function testQuoteCollectTotalsBefore()
    {
        $quoteMock = $this->getMock(
            '\Magento\Sales\Model\Quote',
            [
                'setIsNewGiftWrappingCollecting',
                'setIsNewGiftWrappingTaxCollecting',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));
        $quoteMock->expects($this->once())->method('setIsNewGiftWrappingCollecting')->with(true);
        $quoteMock->expects($this->once())->method('setIsNewGiftWrappingTaxCollecting')->with(true);

        $this->_model->quoteCollectTotalsBefore($this->observerMock);
    }

    public function testPrepareGiftOptionsItems()
    {
        $itemMock = $this->getMock('Magento\Framework\Object',
            [
                'getProduct',
                'getIsVirtual',
                'setIsGiftOptionsAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $productMock = $this->getMock('Magento\Catalog\Model\Product',
            [
                'getGiftWrappingAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getItems')->will($this->returnValue([$itemMock]));
        $itemMock->expects($this->once())->method('getProduct')->will($this->returnValue($productMock));
        $productMock->expects($this->once())->method('getGiftWrappingAvailable')->will($this->returnValue(true));
        $this->helperDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForProduct')->with(true)->will($this->returnValue(true));
        $itemMock->expects($this->once())->method('getIsVirtual')->will($this->returnValue(false));
        $itemMock->expects($this->once())->method('setIsGiftOptionsAvailable')->with(true);

        $this->_model->prepareGiftOptionsItems($this->observerMock);
    }

    public function testPrepareGiftOptionsItemsWithVirtualProduct()
    {
        $itemMock = $this->getMock('Magento\Framework\Object',
            [
                'getProduct',
                'getIsVirtual',
                'setIsGiftOptionsAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $productMock = $this->getMock('Magento\Catalog\Model\Product',
            [
                'getGiftWrappingAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getItems')->will($this->returnValue([$itemMock]));
        $itemMock->expects($this->once())->method('getProduct')->will($this->returnValue($productMock));
        $productMock->expects($this->once())->method('getGiftWrappingAvailable')->will($this->returnValue(true));
        $this->helperDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForProduct')->with(true)->will($this->returnValue(true));
        $itemMock->expects($this->once())->method('getIsVirtual')->will($this->returnValue(true));
        $itemMock->expects($this->never())->method('setIsGiftOptionsAvailable');

        $this->_model->prepareGiftOptionsItems($this->observerMock);
    }

    public function testPrepareGiftOptionsItemsWithNotAllowedProduct()
    {
        $itemMock = $this->getMock('Magento\Framework\Object',
            [
                'getProduct',
                'getIsVirtual',
                'setIsGiftOptionsAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $productMock = $this->getMock('Magento\Catalog\Model\Product',
            [
                'getGiftWrappingAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getItems')->will($this->returnValue([$itemMock]));
        $itemMock->expects($this->once())->method('getProduct')->will($this->returnValue($productMock));
        $productMock->expects($this->once())->method('getGiftWrappingAvailable')->will($this->returnValue(false));
        $this->helperDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForProduct')->with(false)->will($this->returnValue(false));
        $itemMock->expects($this->never())->method('getIsVirtual');
        $itemMock->expects($this->never())->method('setIsGiftOptionsAvailable');

        $this->_model->prepareGiftOptionsItems($this->observerMock);
    }

    public function testSalesEventOrderToQuoteForReorderedOrder()
    {
        $orderMock = $this->getMock('Magento\Sales\Model\Order',
            ['getStore', 'getReordered', '__wakeup'], [], '', false);
        $storeMock = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $orderMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $storeId = 12;
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $orderMock->expects($this->once())->method('getReordered')->will($this->returnValue(true));

        $this->_model->salesEventOrderToQuote($this->observerMock);
    }

    public function testSalesEventOrderToQuoteWithGiftWrappingThatNotAvailableForOrder()
    {
        $orderMock = $this->getMock('Magento\Sales\Model\Order',
            ['getStore', 'getReordered', '__wakeup'], [], '', false);
        $storeMock = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $orderMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $storeId = 12;
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $orderMock->expects($this->once())->method('getReordered')->will($this->returnValue(false));
        $this->helperDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForOrder')
            ->with($storeId)
            ->will($this->returnValue(false));

        $this->_model->salesEventOrderToQuote($this->observerMock);
    }

    public function testSalesEventOrderToQuote()
    {
        $orderMock = $this->getMock('Magento\Sales\Model\Order',
            [
                'getStore',
                'getReordered',
                'getGwId',
                'getGwAllowGiftReceipt',
                'getGwAddCard',
                '__wakeup'
            ],
            [],
            '',
            false);
        $storeMock = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $quoteMock = $this->getMock('Magento\Sales\Model\Quote',
            [
                'setGwId',
                'setGwAllowGiftReceipt',
                'setGwAddCard',
                '__wakeup',
            ], [], '', false);
        $this->observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $orderMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $storeId = 12;
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $orderMock->expects($this->once())->method('getReordered')->will($this->returnValue(false));
        $this->helperDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForOrder')
            ->with($storeId)
            ->will($this->returnValue(true));
        $this->eventMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));
        $orderMock->expects($this->once())->method('getGwId')->will($this->returnValue(1));
        $orderMock->expects($this->once())
            ->method('getGwAllowGiftReceipt')->will($this->returnValue('Gift_recipient'));
        $orderMock->expects($this->once())->method('getGwAddCard')->will($this->returnValue('add_cart'));
        $quoteMock->expects($this->once())->method('setGwId')->with(1)->will($this->returnValue($quoteMock));
        $quoteMock->expects($this->once())
            ->method('setGwAllowGiftReceipt')->with('Gift_recipient')->will($this->returnValue($quoteMock));
        $quoteMock->expects($this->once())
            ->method('setGwAddCard')->with('add_cart')->will($this->returnValue($quoteMock));

        $this->_model->salesEventOrderToQuote($this->observerMock);
    }

    public function testSalesEventOrderItemToQuoteItemWithReorderedOrder()
    {
        $orderMock = $this->getMock('Magento\Sales\Model\Order',
            ['getStore', 'getReordered', '__wakeup'], [], '', false);
        $orderItemMock = $this->getMock('Magento\Sales\Model\Order\Item', ['getOrder', '__wakeup'], [], '', false);
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getOrderItem')->will($this->returnValue($orderItemMock));
        $orderItemMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $orderMock->expects($this->once())->method('getReordered')->will($this->returnValue(true));

        $this->_model->salesEventOrderItemToQuoteItem($this->observerMock);
    }

    public function testSalesEventOrderItemToQuoteItemWithGiftWrappingThatNotAllowedForItems()
    {
        $orderMock = $this->getMock('Magento\Sales\Model\Order',
            ['getStore', 'getReordered', '__wakeup'], [], '', false);
        $orderItemMock = $this->getMock('Magento\Sales\Model\Order\Item', ['getOrder', '__wakeup'], [], '', false);
        $storeMock = $this->getMock('Magento\Store\Model\Store', [], [], '', false);

        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getOrderItem')->will($this->returnValue($orderItemMock));
        $orderItemMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $orderMock->expects($this->once())->method('getReordered')->will($this->returnValue(false));

        $storeId = 12;
        $orderMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->helperDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForItems')
            ->with($storeId)
            ->will($this->returnValue(null));

        $this->_model->salesEventOrderItemToQuoteItem($this->observerMock);
    }

    public function testSalesEventOrderItemToQuoteItem()
    {
        $orderItemMock = $this->getMock('Magento\Sales\Model\Order\Item',
            [
                'getOrder',
                'getGwId',
                'getGwBasePrice',
                'getGwPrice',
                'getGwBaseTaxAmount',
                'getGwTaxAmount',
                '__wakeup'
            ],
            [],
            '',
            false);
        $quoteItemMock = $this->getMock('Magento\Sales\Model\Quote\Item',
            [
                'setGwId',
                'setGwBasePrice',
                'setGwPrice',
                'setGwBaseTaxAmount',
                'setGwTaxAmount',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getOrderItem')->will($this->returnValue($orderItemMock));
        $orderItemMock->expects($this->once())->method('getOrder')->will($this->returnValue(null));
        $this->eventMock->expects($this->once())->method('getQuoteItem')->will($this->returnValue($quoteItemMock));
        $orderItemMock->expects($this->once())->method('getGwId')->will($this->returnValue(11));
        $orderItemMock->expects($this->once())->method('getGwBasePrice')->will($this->returnValue(22));
        $orderItemMock->expects($this->once())->method('getGwPrice')->will($this->returnValue(33));
        $orderItemMock->expects($this->once())->method('getGwBaseTaxAmount')->will($this->returnValue(44));
        $orderItemMock->expects($this->once())->method('getGwTaxAmount')->will($this->returnValue(55));
        $quoteItemMock->expects($this->once())
            ->method('setGwId')
            ->with(11)
            ->will($this->returnValue($quoteItemMock));
        $quoteItemMock->expects($this->once())
            ->method('setGwBasePrice')
            ->with(22)
            ->will($this->returnValue($quoteItemMock));
        $quoteItemMock->expects($this->once())
            ->method('setGwPrice')
            ->with(33)
            ->will($this->returnValue($quoteItemMock));
        $quoteItemMock->expects($this->once())
            ->method('setGwBaseTaxAmount')
            ->with(44)->will($this->returnValue($quoteItemMock));
        $quoteItemMock->expects($this->once())
            ->method('setGwTaxAmount')
            ->with(55)
            ->will($this->returnValue($quoteItemMock));

        $this->_model->salesEventOrderItemToQuoteItem($this->observerMock);
    }

    /**
     * @param float $amount
     * @dataProvider addPaymentGiftWrappingItemTotalCardDataProvider
     */
    public function testAddPaymentGiftWrappingItemTotalCard($amount)
    {
        $salesModel = $this->getMockForAbstractClass('Magento\Payment\Model\Cart\SalesModel\SalesModelInterface');
        $salesModel->expects($this->once())->method('getAllItems')->will($this->returnValue([]));
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
        $cart = $this->getMock('Magento\Payment\Model\Cart', [], [], '', false);
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
        return [[null], [0], [0.12]];
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
        $cart = $this->getMock('Magento\Payment\Model\Cart', [], [], '', false);
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
        $amounts = [null, 0, 0.12];
        $originalItems = [
            [],
            [
                new \Magento\Framework\Object(
                    ['parent_item' => 'something', 'gw_id' => 1, 'gw_base_price' => 0.3]
                ),
                new \Magento\Framework\Object(['gw_id' => null, 'gw_base_price' => 0.3]),
                new \Magento\Framework\Object(['gw_id' => 1, 'gw_base_price' => 0.0]),
                new \Magento\Framework\Object(['gw_id' => 2, 'gw_base_price' => null]),
                new \Magento\Framework\Object(['gw_id' => 3, 'gw_base_price' => 0.12]),
                new \Magento\Framework\Object(['gw_id' => 4, 'gw_base_price' => 2.1])
            ],
        ];
        $itemsPrice = [0, 0.12 + 2.1];
        $data = [];
        foreach ($amounts as $amount) {
            foreach ($originalItems as $i => $originalItemsSet) {
                $items = [];
                foreach ($originalItemsSet as $originalItem) {
                    $items[] = new \Magento\Framework\Object(['original_item' => $originalItem]);
                }
                $data[] = [$items, $amount, $itemsPrice[$i] + (double)$amount];
            }
        }
        return $data;
    }
}
