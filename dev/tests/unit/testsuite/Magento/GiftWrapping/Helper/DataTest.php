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
    protected $quoteDetailsBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteDetailsItemBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxCalculationServiceMock;

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
        $this->quoteDetailsBuilderMock = $this->getMock(
            '\Magento\Tax\Service\V1\Data\QuoteDetailsBuilder',
            [],
            [],
            '',
            false
        );
        $this->quoteDetailsItemBuilderMock = $this->getMock(
            '\Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder',
            [],
            [],
            '',
            false
        );
        $this->taxCalculationServiceMock = $this->getMock('\Magento\Tax\Service\V1\TaxCalculationServiceInterface');
        $this->addressConverterMock = $this->getMock('\Magento\Customer\Model\Address\Converter', [], [], '', false);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject(
            '\Magento\GiftWrapping\Helper\Data',
            [
                'context' => $context,
                'scopeConfig' => $this->scopeConfigMock,
                'storeManager' => $this->storeManagerMock,
                'quoteDetailsBuilder' => $this->quoteDetailsBuilderMock,
                'quoteDetailsItemBuilder' => $this->quoteDetailsItemBuilderMock,
                'taxCalculationService' => $this->taxCalculationServiceMock,
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

    public function testIsGiftWrappingAvailableIfProductConfigExist()
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
        $scopeConfig = Type::DISPLAY_TYPE_BOTH;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displayCartWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingIncludeTaxPriceWhenDisplayTypeIsIncludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_INCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displayCartWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingIncludeTaxPriceWhenDisplayTypeIsExcludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_EXCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertFalse($this->subject->displayCartWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingExcludeTaxPriceWhenDisplayTypeIsIncludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_INCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertFalse($this->subject->displayCartWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingExcludeTaxPriceWhenDisplayTypeIsExcludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_EXCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displayCartWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingBothPricesIsIncludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_EXCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertFalse($this->subject->displayCartWrappingBothPrices($storeMock));
    }

    public function testDisplayCartWrappingBothPricesIsBoth()
    {
        $scopeConfig = Type::DISPLAY_TYPE_BOTH;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displayCartWrappingBothPrices($storeMock));
    }

    public function testDisplayCartCardIncludeTaxPriceIsBoth()
    {
        $scopeConfig = Type::DISPLAY_TYPE_BOTH;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displayCartCardIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartCardIncludeTaxPriceIsExcludingTaxPrice()
    {
        $scopeConfig = Type::DISPLAY_TYPE_EXCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertFalse($this->subject->displayCartCardIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartCardIncludeTaxPriceIsIncludingTaxPrice()
    {
        $scopeConfig = Type::DISPLAY_TYPE_INCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displayCartCardIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartCardBothPricesIncludingTaxPrice()
    {
        $scopeConfig = Type::DISPLAY_TYPE_INCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertFalse($this->subject->displayCartCardBothPrices($storeMock));
    }

    public function testDisplayCartCardBothPricesDisplayTypeBoth()
    {
        $scopeConfig = Type::DISPLAY_TYPE_BOTH;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displayCartCardBothPrices($storeMock));
    }

    public function testDisplaySalesWrappingIncludeTaxPriceDisplayTypeBoth()
    {
        $scopeConfig = Type::DISPLAY_TYPE_BOTH;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displaySalesWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingIncludeTaxPriceDisplayTypeIncludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_INCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displaySalesWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingIncludeTaxPriceDisplayTypeExcludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_EXCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertFalse($this->subject->displaySalesWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingExcludeTaxPriceDisplayTypeExcludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_EXCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displaySalesWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingExcludeTaxPriceDisplayTypeIncludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_INCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertFalse($this->subject->displaySalesWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingBothPricesDisplayTypeIncludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_INCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertFalse($this->subject->displaySalesWrappingBothPrices($storeMock));
    }

    public function testDisplaySalesWrappingBothPricesDisplayTypeBoth()
    {
        $scopeConfig = Type::DISPLAY_TYPE_BOTH;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displaySalesWrappingBothPrices($storeMock));
    }

    public function testDisplaySalesCardIncludeTaxPriceDisplayTypeBoth()
    {
        $scopeConfig = Type::DISPLAY_TYPE_BOTH;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displaySalesCardIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesCardIncludeTaxPriceDisplayTypeIncludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_INCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displaySalesCardIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesCardIncludeTaxPriceDisplayTypeExcludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_EXCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertFalse($this->subject->displaySalesCardIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesCardBothPricesDisplayTypeExcludingTax()
    {
        $scopeConfig = Type::DISPLAY_TYPE_EXCLUDING_TAX;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertFalse($this->subject->displaySalesCardBothPrices($storeMock));
    }

    public function testDisplaySalesCardBothPricesDisplayTypeBoth()
    {
        $scopeConfig = Type::DISPLAY_TYPE_BOTH;
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertTrue($this->subject->displaySalesCardBothPrices($storeMock));
    }
}
