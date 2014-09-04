<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Model;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class CartTest
 */
class CartTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Checkout\Model\Cart */
    protected $cart;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Checkout\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $checkoutSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /** @var \Magento\CatalogInventory\Service\V1\StockItemService|\PHPUnit_Framework_MockObject_MockObject */
    protected $stockItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    protected function setUp()
    {
        $this->checkoutSessionMock = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false);
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $this->scopeConfigMock = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->stockItemMock = $this->getMock(
            'Magento\CatalogInventory\Service\V1\StockItemService',
            [],
            [],
            '',
            false
        );
        $this->quoteMock = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $this->eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->cart = $this->objectManagerHelper->getObject(
            'Magento\Checkout\Model\Cart',
            [
                'scopeConfig' => $this->scopeConfigMock,
                'checkoutSession' => $this->checkoutSessionMock,
                'stockItemService' => $this->stockItemMock,
                'customerSession' => $this->customerSessionMock,
                'eventManager' => $this->eventManagerMock
            ]
        );
    }

    public function testSuggestItemsQty()
    {
        $data = [[], ['qty' => -2], ['qty' => 3], ['qty' => 3.5], ['qty' => 5], ['qty' => 4]];
        $this->quoteMock->expects($this->any())
            ->method('getItemById')
            ->will($this->returnValueMap([
                [2, $this->prepareQuoteItemMock(2)],
                [3, $this->prepareQuoteItemMock(3)],
                [4, $this->prepareQuoteItemMock(4)],
                [5, $this->prepareQuoteItemMock(5)],
            ]));

        $this->stockItemMock->expects($this->any())
            ->method('suggestQty')
            ->will($this->returnValueMap([[4, 3., 3.], [5, 3.5, 3.5]]));

        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($this->quoteMock));

        $this->assertSame(
            [
                [],
                ['qty' => -2],
                ['qty' => 3., 'before_suggest_qty' => 3.],
                ['qty' => 3.5, 'before_suggest_qty' => 3.5],
                ['qty' => 5],
                ['qty' => 4],
            ],
            $this->cart->suggestItemsQty($data)
        );
    }

    public function testUpdateItems()
    {
        $data = [['qty' => 5.5, 'before_suggest_qty' => 5.5]];
        $infoDataObject = $this->objectManagerHelper->getObject('Magento\Framework\Object', ['data' => $data]);

        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($this->quoteMock));
        $this->eventManagerMock->expects($this->at(0))->method('dispatch')->with(
            'checkout_cart_update_items_before',
            ['cart' => $this->cart, 'info' => $infoDataObject]
        );
        $this->eventManagerMock->expects($this->at(1))->method('dispatch')->with(
            'checkout_cart_update_items_after',
            ['cart' => $this->cart, 'info' => $infoDataObject]
        );

        $result = $this->cart->updateItems($data);
        $this->assertInstanceOf('Magento\Checkout\Model\Cart', $result);
    }

    /**
     * @param int|bool $itemId
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function prepareQuoteItemMock($itemId)
    {
        switch ($itemId) {
            case 2:
                $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
                $product->expects($this->once())
                    ->method('getId')
                    ->will($this->returnValue(4));
                break;
            case 3:
                $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
                $product->expects($this->once())
                    ->method('getId')
                    ->will($this->returnValue(5));
                break;
            case 4:
                $product = false;
                break;
            default:
                return false;
        }

        $quoteItem = $this->getMock('Magento\Sales\Model\Quote\Item', [], [], '', false);
        $quoteItem->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));
        return $quoteItem;
    }

    /**
     * @param boolean $useQty
     * @dataProvider useQtyDataProvider
     */
    public function testGetSummaryQty($useQty)
    {
        $quoteId = 1;
        $itemsCount = 1;
        $quoteMock = $this->getMock(
            'Magento\Sales\Model\Quote',
            ['getItemsCount', 'getItemsQty', '__wakeup'],
            [],
            '',
            false
        );

        $this->checkoutSessionMock->expects($this->any())->method('getQuote')->will($this->returnValue($quoteMock));
        $this->checkoutSessionMock->expects($this->at(2))->method('getQuoteId')->will($this->returnValue($quoteId));
        $this->customerSessionMock->expects($this->any())->method('isLoggedIn')->will($this->returnValue(true));

        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with('checkout/cart_link/use_qty', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ->will($this->returnValue($useQty));

        $qtyMethodName = ($useQty) ? 'getItemsQty' : 'getItemsCount';
        $quoteMock->expects($this->once())->method($qtyMethodName)->will($this->returnValue($itemsCount));

        $this->assertEquals($itemsCount, $this->cart->getSummaryQty());
    }

    public function useQtyDataProvider()
    {
        return [
            ['useQty' => true],
            ['useQty' => false]
        ];
    }
}
