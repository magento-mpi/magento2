<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Sales\Total\Quote;

/**
 * Test class for \Magento\Tax\Model\Sales\Total\Quote\Tax
 */
use Magento\Tax\Model\Calculation;
use Magento\TestFramework\Helper\ObjectManager;

class TaxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the specific method
     */
    public function testCollect()
    {
        $objectManager = new ObjectManager($this);
        $taxData = $this->getMock('Magento\Tax\Helper\Data', [], [], '', false);
        $taxConfig = $this->getMockBuilder('\Magento\Tax\Model\Config')
            ->disableOriginalConstructor()
            ->setMethods(['priceIncludesTax'])
            ->getMock();
        $taxConfig
            ->expects($this->any())
            ->method('priceIncludesTax')
            ->will($this->returnValue(true));

        $product = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $item = $this->getMockBuilder('Magento\Sales\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->setMethods(['getParentItem', 'getHasChildren', 'getProduct', 'getQuote', 'getCode', '__wakeup'])
            ->getMock();
        $item
            ->expects($this->any())
            ->method('getParentItem')
            ->will($this->returnValue(null));
        $item
            ->expects($this->any())
            ->method('getHasChildren')
            ->will($this->returnValue(false));
        $item
            ->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue("1"));
        $item
            ->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($product));
        $items = array($item);
        $taxDetails = $this->getMockBuilder('Magento\Tax\Service\V1\Data\TaxDetails')
            ->disableOriginalConstructor()
            ->setMethods(['getItems'])
            ->getMock();
        $taxDetails
            ->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue($items));
        $taxCalculationService = $this->getMockBuilder('\Magento\Tax\Service\V1\TaxCalculationService')
            ->disableOriginalConstructor()
            ->setMethods(['calculateTax'])
            ->getMock();
        $taxCalculationService
            ->expects($this->any())
            ->method('calculateTax')
            ->will($this->returnValue($taxDetails));

        $quoteDetailsBuilder = $this->getMockBuilder('\Magento\Tax\Service\V1\Data\QuoteDetailsBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['getItemBuilder', 'setBillingAddress', 'setShippingAddress', 'getAddressBuilder',
                'getTaxClassKeyBuilder', 'create'])
            ->getMock();

        $taxClassKeyBuilder = $this->getMockBuilder('\Magento\Tax\Service\V1\Data\TaxClassKeyBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['setType', 'setValue', 'create'])
            ->getMock();
        $taxClassKeyBuilder
            ->expects($this->any())
            ->method('setType')
            ->will($this->returnValue($taxClassKeyBuilder));
        $taxClassKeyBuilder
            ->expects($this->any())
            ->method('setValue')
            ->will($this->returnValue($taxClassKeyBuilder));
        $taxClassKeyBuilder
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($taxClassKeyBuilder));

        $itemBuilder = $this->getMockBuilder('\Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['getTaxClassKeyBuilder', 'setTaxClassKey', 'create', 'getAssociatedTaxables'])
            ->getMock();
        $itemBuilder
            ->expects($this->any())
            ->method('getTaxClassKeyBuilder')
            ->will($this->returnValue($taxClassKeyBuilder));
        $itemBuilder
            ->expects($this->any())
            ->method('setTaxClassKey')
            ->will($this->returnValue($itemBuilder));
        $itemBuilder
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($itemBuilder));
        $itemBuilder
            ->expects($this->any())
            ->method('getAssociatedTaxables')
            ->will($this->returnValue(null));

        $regionBuilder = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\RegionBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['setRegionId', 'create'])
            ->getMock();

        $addressBuilder = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\AddressBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['getRegionBuilder', 'create'])
            ->getMock();
        $region = $this->getMock('Magento\Customer\Service\V1\Data\Region', [], [], '', false);
        $regionBuilder
            ->expects($this->any())
            ->method('setRegionId')
            ->will($this->returnValue($regionBuilder));
        $regionBuilder
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($region));
        $addressBuilder
            ->expects($this->any())
            ->method('getRegionBuilder')
            ->will($this->returnValue($regionBuilder));

        $quoteDetails = $this->getMock('Magento\Tax\Service\V1\Data\QuoteDetails', [], [], '', false);
        $quoteDetailsBuilder
            ->expects($this->any())
            ->method('getItemBuilder')
            ->will($this->returnValue($itemBuilder));
        $quoteDetailsBuilder
            ->expects($this->any())
            ->method('setBillingAddress')
            ->will($this->returnValue($quoteDetailsBuilder));
        $quoteDetailsBuilder
            ->expects($this->any())
            ->method('setShippingAddress')
            ->will($this->returnValue($quoteDetailsBuilder));
        $quoteDetailsBuilder
            ->expects($this->any())
            ->method('getAddressBuilder')
            ->will($this->returnValue($addressBuilder));
        $quoteDetailsBuilder
            ->expects($this->any())
            ->method('getTaxClassKeyBuilder')
            ->will($this->returnValue($taxClassKeyBuilder));
        $quoteDetailsBuilder
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($quoteDetails));

        $taxTotalsCalcModel = new Tax($taxConfig, $taxCalculationService, $quoteDetailsBuilder, $taxData);

        $store = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['convertPrice', '__wakeup'])
            ->getMock();
        $quote = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $quote
            ->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));
        $address = $this->getMockBuilder('\Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->setMethods(['addTotalAmount', 'addBaseTotalAmount', 'setExtraTaxableDetails', 'getAssociatedTaxables',
                          'getAllItems', 'getAllNonNominalItems', 'getQuote', 'getBillingAddress', 'getRegionId',
                          '__wakeup'])
            ->getMock();
        $item
            ->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));
        $address
            ->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));
        $address
            ->expects($this->any())
            ->method('getAssociatedTaxables')
            ->will($this->returnValue(array()));
        $address
            ->expects($this->any())
            ->method('getAllNonNominalItems')
            ->will($this->returnValue($items));
        $address
            ->expects($this->any())
            ->method('getRegionId')
            ->will($this->returnValue($region));
        $quote
            ->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($address));
        $addressBuilder
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($address));

        $taxTotalsCalcModel->collect($address);
    }

    /**
     * Tests the specific method
     *
     * @param string $calculationSequence
     * @param string $keyExpected
     * @param string $keyAbsent
     * @dataProvider dataProviderProcessConfigArray
     */
    public function testProcessConfigArray($calculationSequence, $keyExpected, $keyAbsent)
    {
        $taxData = $this->getMock('Magento\Tax\Helper\Data', [], [], '', false);
        $taxData
            ->expects($this->any())
            ->method('getCalculationSequence')
            ->will($this->returnValue($calculationSequence));

        $taxConfig = $this->getMock('\Magento\Tax\Model\Config', [], [], '', false);
        $taxCalculationService = $this->getMock('\Magento\Tax\Service\V1\TaxCalculationService', [], [], '', false);
        $quoteDetailsBuilder = $this->getMock('\Magento\Tax\Service\V1\Data\QuoteDetailsBuilder', [], [], '', false);

        /** @var \Magento\Tax\Model\Sales\Total\Quote\Tax */
        $taxTotalsCalcModel = new Tax($taxConfig, $taxCalculationService, $quoteDetailsBuilder, $taxData);
        $array = $taxTotalsCalcModel->processConfigArray([], null);
        $this->assertArrayHasKey($keyExpected, $array, 'Did not find the expected array key: ' . $keyExpected);
        $this->assertArrayNotHasKey($keyAbsent, $array, 'Should not have found the array key; ' . $keyAbsent);
    }

    /**
     * @return array
     */
    public function dataProviderProcessConfigArray()
    {
        return [
            [Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL, 'before', 'after'],
            [Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL, 'after', 'before'],
            [Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL, 'after', 'before'],
            [Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL, 'after', 'before']
        ];
    }

    /**
     * Tests the specific method
     */
    public function testMapQuoteExtraTaxables()
    {
        $objectManager = new ObjectManager($this);
        $taxData = $this->getMock('Magento\Tax\Helper\Data', [], [], '', false);
        $taxConfig = $this->getMock('\Magento\Tax\Model\Config', [], [], '', false);
        $taxCalculationService = $this->getMock('\Magento\Tax\Service\V1\TaxCalculationService', [], [], '', false);
        $quoteDetailsBuilder = $this->getMock('\Magento\Tax\Service\V1\Data\QuoteDetailsBuilder', [], [], '', false);

        $taxTotalsCalcModel = new Tax($taxConfig, $taxCalculationService, $quoteDetailsBuilder, $taxData);
        $taxClassKeyBuilder = $this->getMockBuilder('\Magento\Tax\Service\V1\Data\TaxClassKeyBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['setType', 'setValue', 'create'])
            ->getMock();
        $taxClassKeyBuilder
            ->expects($this->any())
            ->method('setType')
            ->will($this->returnValue($taxClassKeyBuilder));
        $taxClassKeyBuilder
            ->expects($this->any())
            ->method('setValue')
            ->will($this->returnValue($taxClassKeyBuilder));
        $taxClassKeyBuilder
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($taxClassKeyBuilder));

        $itemBuilder = $this->getMockBuilder('\Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['getTaxClassKeyBuilder', 'setTaxClassKey', 'create', 'getAssociatedTaxables'])
            ->getMock();
        $itemBuilder
            ->expects($this->any())
            ->method('getTaxClassKeyBuilder')
            ->will($this->returnValue($taxClassKeyBuilder));
        $itemBuilder
            ->expects($this->any())
            ->method('setTaxClassKey')
            ->will($this->returnValue($itemBuilder));
        $itemBuilder
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($itemBuilder));
        $itemBuilder
            ->expects($this->any())
            ->method('getAssociatedTaxables')
            ->will($this->returnValue(null));

        $address = $this->getMockBuilder('\Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->setMethods(['getAssociatedTaxables', '__wakeup'])
            ->getMock();
        $address
            ->expects($this->any())
            ->method('getAssociatedTaxables')
            ->will($this->returnValue(array()));

        $taxTotalsCalcModel->mapQuoteExtraTaxables($itemBuilder, $address, false);
    }

    /**
     * Tests the specific method
     */
    public function testFetch()
    {
        $objectManager = new ObjectManager($this);
        $taxData = $this->getMock('Magento\Tax\Helper\Data', [], [], '', false);

        $taxConfig = $this->getMockBuilder('\Magento\Tax\Model\Config')
            ->disableOriginalConstructor()
            ->setMethods(['displayCartTaxWithGrandTotal', 'displayCartZeroTax', 'displayCartSubtotalBoth'])
            ->getMock();
        $taxConfig
            ->expects($this->once())
            ->method('displayCartTaxWithGrandTotal')
            ->will($this->returnValue(true));
        $taxConfig
            ->expects($this->once())
            ->method('displayCartZeroTax')
            ->will($this->returnValue(true));
        $taxConfig
            ->expects($this->once())
            ->method('displayCartSubtotalBoth')
            ->will($this->returnValue(true));

        $taxCalculationService = $this->getMock('\Magento\Tax\Service\V1\TaxCalculationService', [], [], '', false);
        $quoteDetailsBuilder = $this->getMock('\Magento\Tax\Service\V1\Data\QuoteDetailsBuilder', [], [], '', false);

        $taxTotalsCalcModel = new Tax($taxConfig, $taxCalculationService, $quoteDetailsBuilder, $taxData);

        $appliedTaxes = unserialize('a:1:{s:7:"TX Rate";a:9:{s:6:"amount";d:8;s:11:"base_amount";d:8;s:7:"percent";d:10;s:2:"id";s:7:"TX Rate";s:5:"rates";a:1:{i:0;a:3:{s:7:"percent";d:10;s:4:"code";s:7:"TX Rate";s:5:"title";s:7:"TX Rate";}}s:7:"item_id";s:1:"1";s:9:"item_type";s:7:"product";s:18:"associated_item_id";N;s:7:"process";i:0;}}');
        $store = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['convertPrice', '__wakeup'])
            ->getMock();
        $quote = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $items = array();

        $address = $this->getMockBuilder('\Magento\Sales\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->setMethods(['getAppliedTaxes', 'getQuote', 'getAllNonNominalItems', 'getGrandTotal', '__wakeup', 'addTotal'])
            ->getMock();
        $address
            ->expects($this->once())
            ->method('getAppliedTaxes')
            ->will($this->returnValue($appliedTaxes));
        $address
            ->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));
        $address
            ->expects($this->once())
            ->method('getAllNonNominalItems')
            ->will($this->returnValue($items));
        $address
            ->expects($this->any())
            ->method('getGrandTotal')
            ->will($this->returnValue(88));
        $quote
            ->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $taxTotalsCalcModel->fetch($address);
    }

    /**
     * Tests the specific method
     */
    public function testGetLabel()
    {
        $objectManager = new ObjectManager($this);
        $taxData = $this->getMock('Magento\Tax\Helper\Data', [], [], '', false);

        $taxConfig = $this->getMock('\Magento\Tax\Model\Config', [], [], '', false);
        $taxCalculationService = $this->getMock('\Magento\Tax\Service\V1\TaxCalculationService', [], [], '', false);
        $quoteDetailsBuilder = $this->getMock('\Magento\Tax\Service\V1\Data\QuoteDetailsBuilder', [], [], '', false);

        $taxTotalsCalcModel = new Tax($taxConfig, $taxCalculationService, $quoteDetailsBuilder, $taxData);
        $this->assertSame($taxTotalsCalcModel->getLabel(), __('Tax'));
    }
}
