<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Pricing\Price;

/**
 * Class CustomOptionTest
 *
 * @package Magento\ConfigurableProduct\Pricing\Price;
 */
class AttributePriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\PriceModifierInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceModifier;

    /**
     * @var \Magento\Pricing\Amount\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItemMock;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculatorMock;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\ConfigurableProduct\Pricing\Price\AttributePrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attribute;

    /**
     * @var \Magento\Catalog\Pricing\Price\RegularPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $regularPriceMock;

    /**
     * @var \Magento\Catalog\Helper\Product\Price|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceHelperMock;

    /**
     * @var \Magento\Tax\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxHelpermock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute|
     * \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * Test setUp
     */
    protected function setUp()
    {
        $this->saleableItemMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['hasPreconfiguredValues', '__wakeUp'],
            [],
            '',
            false
        );
        $this->priceInfoMock = $this->getMock('Magento\Pricing\PriceInfo\Base', [], [], '', false);
        $this->amountMock = $this->getMock('Magento\Pricing\Amount\Base', [], [], '', false);
        $this->calculatorMock = $this->getMock('Magento\Pricing\Adjustment\Calculator', [], [], '', false);
        $this->regularPriceMock = $this->getMock('Magento\Catalog\Pricing\Price\RegularPrice', [], [], '', false);
        $this->priceModifier = $this->getMock(
            'Magento\Catalog\Model\Product\PriceModifierInterface',
            [],
            [],
            '',
            false
        );
        $this->taxHelpermock = $this->getMock('Magento\Tax\Helper\Data', [], [], '', false);
        $this->priceHelperMock = $this->getMock('Magento\Catalog\Helper\Product\Price', [], [], '', false);
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', ['getStore'], [], '', false);
        $this->attributeMock = $this->getMock(
            'Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute',
            [],
            [],
            '',
            false
        );

        $this->saleableItemMock->expects($this->atLeastOnce())
            ->method('hasPreconfiguredValues')
            ->will($this->returnValue(false));

        $this->attribute = new AttributePrice(
            $this->saleableItemMock,
            1,
            $this->calculatorMock,
            $this->priceModifier,
            $this->taxHelpermock,
            $this->priceHelperMock,
            $this->storeManagerMock
        );
    }

    public function testPrepareJsonAttributes()
    {
        $price = 43;

        $attributeCode = 'color_config';
        $attributeId = 176;

        $options = ['images' => [],];

        $store = $this->getMock('Magento\Store\Model\Store', ['convertPrice'], [], '', false);
        $configAttributesMock = $this->getMock(
            'Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Collection',
            [],
            [],
            '',
            false
        );
        $productAttributeMock = $this->getMock('Magento\Catalog\Model\Resource\Eav\Attribute',
            ['getId', 'getAttributeCode'],
            [],
            '',
            false
        );
        $attributeMock = $this->getMock('Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute',
            ['getProductAttribute', 'getLabel'],
            [],
            '',
            false
        );

        $typeMock = $this->getMock(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable',
            ['getConfigurableAttributes'],
            [],
            '',
            false,
            false
        );
        $productAttributeMock->expects($this->atLeastOnce())
            ->method('getId')
            ->will($this->returnValue($attributeId));
        $productAttributeMock->expects($this->atLeastOnce())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));

        $attributeMock->expects($this->atLeastOnce())
            ->method('getConfigurableAttributes')
            ->will($this->returnValue($productAttributeMock));
        $attributeMock->expects($this->atLeastOnce())
            ->method('getLabel')
            ->will($this->returnValue($attributeCode));

        $this->saleableItemMock->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($typeMock));
        $typeMock->expects($this->once())
            ->method('getConfigurableAttributes')
            ->will($this->returnValue($configAttributesMock));

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));
        $store->expects($this->once())
            ->method('convertPrice')
            ->with($this->equalTo($price))
            ->will($this->returnValue($price));

        $this->attribute->prepareJsonAttributes($options);
    }

    /**
     * Test case for getOptionValueOldAmount with percent value
     */
    public function testGetOptionValueOldAmount()
    {
        $amount = 50;
        $value = [
            'is_percent' => 1,
            'pricing_value' => 103,
        ];
        $pricingValue = $expectedResult = $amount * $value['pricing_value'] / 100;
        $this->preparePrice($amount);
        $this->calculatorMock->expects($this->any())
            ->method('getAmount')
            ->with($pricingValue, $this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($pricingValue));

        $result = $this->attribute->getOptionValueOldAmount($value);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test case for getOptionValueOldAmount with fixed value
     */
    public function testGetOptionValueOldAmountFixedValue()
    {
        $amount = 103;
        $value = [
            'is_percent' => 0,
            'pricing_value' => 103,
        ];
        $this->calculatorMock->expects($this->once())
            ->method('getAmount')
            ->with($value['pricing_value'], $this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($amount));

        $result = $this->attribute->getOptionValueOldAmount($value);
        $this->assertEquals($amount, $result);
    }

    /**
     * @param $id
     * @param $price
     * @param $oldPrice
     * @param $inclTaxPrice
     * @param $exclTaxPrice
     * @param $products
     * @dataProvider getUrlDataProvider
     */
    public function preparePrice($id, $price, $oldPrice, $inclTaxPrice, $exclTaxPrice, $products)
    {
        $priceCode = 'final_price';

        $this->saleableItemMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));
        $this->priceInfoMock->expects($this->atLeastOnce())
            ->method('getPrice')
            ->with($this->equalTo($priceCode))
            ->will($this->returnValue($this->regularPriceMock));
        $this->regularPriceMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($amount));
    }

    public function preparePriceDataProvider()
    {
        return [
            0 => [
                'id' => 20,
                'price' => 2.4,
                'oldPrice' => 2.6,
                'inclTaxPrice' => 2.6,
                'exlTaxPrice' => 2.4,
                'products' => [0 => 12]
            ],
            1 => [
                'id' => 21,
                'price' => 9.24,
                'oldPrice' => 10,
                'inclTaxPrice' => 10,
                'exlTaxPrice' => 9.24,
                'products' => [0 => 13]
            ],
            2 => [
                'id' => 22,
                'price' => 13.86,
                'oldPrice' => 15,
                'inclTaxPrice' => 15,
                'exlTaxPrice' => 13.86,
                'products' => [0 => 14]
            ]
        ];
    }

    /**
     * Test case for getOptionValueAmount with percent value
     */
    public function testGetOptionValueAmount()
    {
        $amount = 50;
        $value = [
            'is_percent' => 1,
            'pricing_value' => 103,
        ];
        $pricingValue = $expectedResult = $amount * $value['pricing_value'] / 100;
        $this->preparePrice($amount);
        $this->calculatorMock->expects($this->atLeastOnce())
            ->method('getAmount')
            ->with($pricingValue, $this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($pricingValue));
        $this->priceModifier->expects($this->once())
            ->method('modifyPrice')
            ->with($this->equalTo($pricingValue), $this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($pricingValue));

        $result = $this->attribute->getOptionValueAmount($value);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test case for getOptionValueAmount with fixed value
     */
    public function testGetOptionValueAmountFixedValue()
    {
        $value = [
            'is_percent' => 0,
            'pricing_value' => 103,
        ];
        $pricingValue = $expectedResult = $value['pricing_value'];
        $this->calculatorMock->expects($this->atLeastOnce())
            ->method('getAmount')
            ->with($pricingValue, $this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($pricingValue));
        $this->priceModifier->expects($this->once())
            ->method('modifyPrice')
            ->with($this->equalTo($pricingValue), $this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($pricingValue));

        $result = $this->attribute->getOptionValueAmount($value);
        $this->assertEquals($expectedResult, $result);
    }

    protected function prepareTaxConfig()
    {
        $this->taxHelpermock->expects($this->once())
            ->method('priceIncludesTax')
            ->will($this->returnValue(false));
        $this->taxHelpermock->expects($this->once())
            ->method('displayPriceIncludingTax')
            ->will($this->returnValue(false));
        $this->taxHelpermock->expects($this->once())
            ->method('displayBothPrices')
            ->will($this->returnValue(false));

        $defaultTax = 2.5;
        $currentTax = 2;

        $taxConfig = [
            'includeTax' => false,
            'showIncludeTax' => false,
            'showBothPrices' => false,
            'defaultTax' => $defaultTax,
            'currentTax' => $currentTax,
            'inclTaxTitle' => __('Incl. Tax')
        ];
        return $taxConfig;
    }

    protected function mockDefaultTax()
    {
        $taxClassId = 2;
        $request = new \Magento\Object();
        $rate = 2.5;

        $request->setProductClassId($taxClassId);
        $this->priceHelperMock->expects($this->atLeastOnce())
            ->method('getRate')
            ->with($this->equalTo($request))
            ->will($this->returnValue($rate));
        $this->saleableItemMock->expects($this->atLeastOnce())
            ->method('getTaxClassId')
            ->will($this->returnValue($taxClassId));
        $this->priceHelperMock->expects($this->atLeastOnce())
            ->method('getRateRequest')
            ->with($this->equalTo('false'), $this->equalTo('false'), $this->equalTo('false'))
            ->will($this->returnValue($request));
    }

    protected function mockCurrentTax()
    {
        $taxClassId = 1;
        $request = new \Magento\Object();
        $rate = 2;

        $request->setProductClassId($taxClassId);
        $this->priceHelperMock->expects($this->atLeastOnce())
            ->method('getRate')
            ->with($this->equalTo($request))
            ->will($this->returnValue($rate));
        $this->saleableItemMock->expects($this->atLeastOnce())
            ->method('getTaxClassId')
            ->will($this->returnValue($taxClassId));
        $this->priceHelperMock->expects($this->atLeastOnce())
            ->method('getRateRequest')
            ->will($this->returnValue($request));
    }
} 
