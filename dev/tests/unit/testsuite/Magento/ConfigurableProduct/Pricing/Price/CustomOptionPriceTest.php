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
class CustomOptionPriceTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\ConfigurableProduct\Pricing\Price\CustomOptionPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customOptionPrice;

    /**
     * @var \Magento\Catalog\Pricing\Price\RegularPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $regularPriceMock;

    /**
     * Test setUp
     */
    protected function setUp()
    {
        $this->saleableItemMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            [],
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

        $this->customOptionPrice = new CustomOptionPrice(
            $this->saleableItemMock,
            1,
            $this->calculatorMock,
            $this->priceModifier
        );
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

        $result = $this->customOptionPrice->getOptionValueOldAmount($value);
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

        $result = $this->customOptionPrice->getOptionValueOldAmount($value);
        $this->assertEquals($amount, $result);
    }

    /**
     * Prepare price
     *
     * @param int $amount
     */
    protected function preparePrice($amount)
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

        $result = $this->customOptionPrice->getOptionValueAmount($value);
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

        $result = $this->customOptionPrice->getOptionValueAmount($value);
        $this->assertEquals($expectedResult, $result);
    }
} 
