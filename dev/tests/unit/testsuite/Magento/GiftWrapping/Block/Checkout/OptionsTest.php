<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Block\Checkout;

use Magento\TestFramework\Helper\ObjectManager;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\GiftWrapping\Block\Checkout\Options
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $wrappingDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreDataMock;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $checkoutItems = [
            'onepage' => [
                'order_level' => 'quote',
                'item_level' => 'quote_item',
            ],
            'multishipping' => [
                'order_level' => 'quote_address',
                'item_level' => 'quote_address_item',
            ],
        ];

        $this->checkoutSessionMock = $this->getMock('\Magento\Checkout\Model\Session', ['getQuote'], [], '', false);
        $this->wrappingDataMock = $this->getMock('\Magento\GiftWrapping\Helper\Data', [], [], '', false);
        $this->coreDataMock = $this->getMock('\Magento\Core\Helper\Data', [], [], '', false);

        $this->block = $this->objectManager->getObject(
            '\Magento\GiftWrapping\Block\Checkout\Options',
            [
                'checkoutSession' => $this->checkoutSessionMock,
                'checkoutItems' => $checkoutItems,
                'giftWrappingData' => $this->wrappingDataMock,
                'coreData' => $this->coreDataMock
            ]
        );
    }

    /**
     * @dataProvider getCheckoutTypeVariableDataProvider
     * @param bool $isMultiShipping
     * @param string $level
     * @param string $expectedResult
     */
    public function testGetCheckoutTypeVariable($isMultiShipping, $level, $expectedResult)
    {
        $quoteMock = $this->getMock(
            '\Magento\Sales\Model\Quote',
            ['getIsMultiShipping', '__wakeup'],
            [],
            '',
            false
        );

        $quoteMock->expects($this->once())
            ->method('getIsMultiShipping')
            ->will($this->returnValue($isMultiShipping));

        $this->checkoutSessionMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        $this->assertEquals($expectedResult, $this->block->getCheckoutTypeVariable($level));
    }

    public function getCheckoutTypeVariableDataProvider()
    {
        return [
            'onepage_order_level' => [false, 'order_level', 'quote'],
            'onepage_item_level' => [false, 'item_level', 'quote_item'],
            'multishipping_order_level' => [true, 'order_level', 'quote_address'],
            'multishipping_item_level' => [true, 'item_level', 'quote_address_item'],
        ];
    }

    public function testGetCheckoutTypeVariableException()
    {
        $level = 'wrong_level';
        $quoteMock = $this->getMock(
            '\Magento\Sales\Model\Quote',
            ['getIsMultiShipping', '__wakeup'],
            [],
            '',
            false
        );

        $quoteMock->expects($this->once())
            ->method('getIsMultiShipping')
            ->will($this->returnValue(false));

        $this->checkoutSessionMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        $this->setExpectedException('InvalidArgumentException', 'Invalid level: ' . $level);
        $this->block->getCheckoutTypeVariable($level);
    }

    public function testGetDisplayWrappingBothPrices()
    {
        $this->wrappingDataMock->expects($this->once())
            ->method('displayCartWrappingBothPrices')
            ->will($this->returnValue(true));
        $this->assertTrue($this->block->getDisplayWrappingBothPrices());
    }

    public function testGetDisplayCardBothPrices()
    {
        $this->wrappingDataMock->expects($this->once())
            ->method('displayCartCardBothPrices')
            ->will($this->returnValue(true));
        $this->assertTrue($this->block->getDisplayCardBothPrices());
    }

    public function testGetDisplayWrappingIncludeTaxPrice()
    {
        $this->wrappingDataMock->expects($this->once())
            ->method('displayCartWrappingIncludeTaxPrice')
            ->will($this->returnValue(true));
        $this->assertTrue($this->block->getDisplayWrappingIncludeTaxPrice());
    }

    public function testGetDisplayCardIncludeTaxPrice()
    {
        $this->wrappingDataMock->expects($this->once())
            ->method('displayCartCardIncludeTaxPrice')
            ->will($this->returnValue(true));
        $this->assertTrue($this->block->getDisplayCardIncludeTaxPrice());
    }

    public function testGetAllowPrintedCard()
    {
        $this->wrappingDataMock->expects($this->once())
            ->method('allowPrintedCard')
            ->will($this->returnValue(true));
        $this->assertTrue($this->block->getAllowPrintedCard());
    }

    public function testGetAllowGiftReceipt()
    {
        $this->wrappingDataMock->expects($this->once())
            ->method('allowGiftReceipt')
            ->will($this->returnValue(true));
        $this->assertTrue($this->block->getAllowGiftReceipt());
    }

    public function testGetAllowForOrder()
    {
        $this->wrappingDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForOrder')
            ->will($this->returnValue(true));
        $this->assertTrue($this->block->getAllowForOrder());
    }

    public function testGetAllowForItems()
    {
        $this->wrappingDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForItems')
            ->will($this->returnValue(true));
        $this->assertTrue($this->block->getAllowForItems());
    }

    public function testCalculatePrice()
    {
        $includeTax = true;
        $basePrice = 99.99;
        $price = 109.99;
        $taxClass = 'tax_class';
        $currency = 100.00;
        $itemMock = $this->getMock('\Magento\Framework\Object', ['setTaxClassId'], [], '', false);
        $shipAddressMock = $this->getMock('\Magento\Sales\Model\Quote\Address', [], [], '', false);

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', ['getBillingAddress', '__wakeup'], [], '', false);
        $this->checkoutSessionMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));

        $billAddressMock = $this->getMock('\Magento\Sales\Model\Quote\Address', [], [], '', false);
        $quoteMock->expects($this->once())->method('getBillingAddress')->will($this->returnValue($billAddressMock));

        $this->wrappingDataMock->expects($this->once())
            ->method('getWrappingTaxClass')
            ->will($this->returnValue($taxClass));
        $itemMock->expects($this->once())->method('setTaxClassId')->with($taxClass)->will($this->returnSelf());

        $this->wrappingDataMock->expects($this->once())
            ->method('getPrice')
            ->with($itemMock, $basePrice, $includeTax, $shipAddressMock, $billAddressMock)
            ->will($this->returnValue($price));

        $this->coreDataMock->expects($this->once())
            ->method('currency')
            ->with($price, true, false)
            ->will($this->returnValue($currency));

        $this->assertEquals(
            $currency,
            $this->block->calculatePrice($itemMock, $basePrice, $shipAddressMock, $includeTax));
    }
}
