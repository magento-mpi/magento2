<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Helper;

use Magento\GiftWrapping\Model\System\Config\Source\Display\Type;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $qDetailsBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxCalcServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressConverterMock;

    /**
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $subject;

    protected function setUp()
    {
        $context = $this->getMock('\Magento\Framework\App\Helper\Context', [], [], '', false);
        $this->scopeConfigMock = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->qDetailsBuilderMock = $this->getMock(
            '\Magento\Tax\Service\V1\Data\QuoteDetailsBuilder',
            [],
            [],
            '',
            false
        );
        $this->itemBuilderMock = $this->getMock(
            '\Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder',
            [],
            [],
            '',
            false
        );
        $this->taxCalcServiceMock = $this->getMock('\Magento\Tax\Service\V1\TaxCalculationServiceInterface');
        $this->addressConverterMock = $this->getMock('\Magento\Customer\Model\Address\Converter', [], [], '', false);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject(
            '\Magento\GiftWrapping\Helper\Data',
            [
                'context' => $context,
                'scopeConfig' => $this->scopeConfigMock,
                'storeManager' => $this->storeManagerMock,
                'quoteDetailsBuilder' => $this->qDetailsBuilderMock,
                'quoteDetailsItemBuilder' => $this->itemBuilderMock,
                'taxCalculationService' => $this->taxCalcServiceMock,
                'addressConverter' => $this->addressConverterMock
            ]
        );
    }

    public function testIsGiftWrappingAvailableIfProductConfigIsNull()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOWED_FOR_ITEMS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->isGiftWrappingAvailableForProduct(null, $storeMock));
    }

    public function testIsGiftWrappingAvailableIfProductConfigIsEmpty()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOWED_FOR_ITEMS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->isGiftWrappingAvailableForProduct('', $storeMock));
    }

    public function testIsGiftWrappingAvailableIfProductConfigExists()
    {
        $productConfig = ['option' => 'config'];
        $this->assertEquals($productConfig, $this->subject->isGiftWrappingAvailableForProduct($productConfig));
    }

    public function testIsGiftWrappingAvailableForItems()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOWED_FOR_ITEMS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->isGiftWrappingAvailableForItems($storeMock));
    }

    public function testIsGiftWrappingAvailableForOrder()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOWED_FOR_ORDER,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->isGiftWrappingAvailableForOrder($storeMock));
    }

    public function testGetWrappingTaxClass()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_TAX_CLASS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->getWrappingTaxClass($storeMock));
    }

    public function testAllowPrintedCard()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOW_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->allowPrintedCard($storeMock));
    }

    public function testAllowGiftReceipt()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOW_GIFT_RECEIPT,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->allowGiftReceipt($storeMock));
    }

    public function testGetPrintedCardPrice()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRINTED_CARD_PRICE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->getPrintedCardPrice($storeMock));
    }

    public function testDisplayCartWrappingIncludeTaxPriceWhenDisplayTypeIsBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displayCartWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingIncludeTaxPriceWhenDisplayTypeIsIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertTrue($this->subject->displayCartWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingIncludeTaxPriceWhenDisplayTypeIsExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displayCartWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingExcludeTaxPriceWhenDisplayTypeIsIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertFalse($this->subject->displayCartWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingExcludeTaxPriceWhenDisplayTypeIsExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertTrue($this->subject->displayCartWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingBothPricesIsIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displayCartWrappingBothPrices($storeMock));
    }

    public function testDisplayCartWrappingBothPricesIsBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displayCartWrappingBothPrices($storeMock));
    }

    public function testDisplayCartCardIncludeTaxPriceIsBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displayCartCardIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartCardIncludeTaxPriceIsExcludingTaxPrice()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displayCartCardIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartCardIncludeTaxPriceIsIncludingTaxPrice()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertTrue($this->subject->displayCartCardIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartCardBothPricesIncludingTaxPrice()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertFalse($this->subject->displayCartCardBothPrices($storeMock));
    }

    public function testDisplayCartCardBothPricesDisplayTypeBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displayCartCardBothPrices($storeMock));
    }

    public function testDisplaySalesWrappingIncludeTaxPriceDisplayTypeBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displaySalesWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingIncludeTaxPriceDisplayTypeIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertTrue($this->subject->displaySalesWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingIncludeTaxPriceDisplayTypeExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displaySalesWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingExcludeTaxPriceDisplayTypeExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertTrue($this->subject->displaySalesWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingExcludeTaxPriceDisplayTypeIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertFalse($this->subject->displaySalesWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingBothPricesDisplayTypeIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertFalse($this->subject->displaySalesWrappingBothPrices($storeMock));
    }

    public function testDisplaySalesWrappingBothPricesDisplayTypeBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displaySalesWrappingBothPrices($storeMock));
    }

    public function testDisplaySalesCardIncludeTaxPriceDisplayTypeBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displaySalesCardIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesCardIncludeTaxPriceDisplayTypeIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertTrue($this->subject->displaySalesCardIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesCardIncludeTaxPriceDisplayTypeExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displaySalesCardIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesCardBothPricesDisplayTypeExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displaySalesCardBothPrices($storeMock));
    }

    public function testDisplaySalesCardBothPricesDisplayTypeBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displaySalesCardBothPrices($storeMock));
    }
}
