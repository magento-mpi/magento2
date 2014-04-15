<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Pricing\Price as CatalogPrice;

class BasePriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BasePrice
     */
    protected $model;

    /**
     * @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleable;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfo;

    /**
     * @var float
     */
    protected $quantity;

    public function setUp()
    {
        $this->quantity = 1.5;

        $this->saleable = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $this->priceInfo = $this->getMock('Magento\Pricing\PriceInfoInterface');

        $this->saleable->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfo));

        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectHelper->getObject('Magento\Bundle\Pricing\Price\BasePrice', [
            'salableItem' => $this->saleable,
            'quantity' => $this->quantity
        ]);
    }

    /**
     * @covers \Magento\Bundle\Pricing\Price\BasePrice::applyDiscount
     * @covers \Magento\Bundle\Pricing\Price\BasePrice::getValue
     */
    public function testGetValue()
    {
        $priceValues = [115, 90, 75];
        $tearPriceValue = 15;
        $groupPriceValue = 10;
        $specialPriceValue = 40;
        $result = 45;

        $pricesIncludedInBase = [];
        foreach ($priceValues as $priceValue) {
            $price = $this->getMock('Magento\Pricing\Price\PriceInterface');
            $price->expects($this->atLeastOnce())
                ->method('getValue')
                ->will($this->returnValue($priceValue));
            $pricesIncludedInBase[] = $price;
        }

        $this->priceInfo->expects($this->once())
            ->method('getPricesIncludedInBase')
            ->will($this->returnValue($pricesIncludedInBase));

        $tearPrice = $this->getMock('Magento\Pricing\Price\PriceInterface');
        $tearPrice->expects($this->atLeastOnce())
            ->method('getValue')
            ->will($this->returnValue($tearPriceValue));

        $groupPrice = $this->getMock('Magento\Pricing\Price\PriceInterface');
        $groupPrice->expects($this->atLeastOnce())
            ->method('getValue')
            ->will($this->returnValue($groupPriceValue));

        $specialPrice = $this->getMock('Magento\Pricing\Price\PriceInterface');
        $specialPrice->expects($this->atLeastOnce())
            ->method('getValue')
            ->will($this->returnValue($specialPriceValue));

        $this->priceInfo->expects($this->any())
            ->method('getPrice')
            ->will($this->returnValueMap([
                [CatalogPrice\TierPrice::PRICE_CODE, $this->quantity, $tearPrice],
                [CatalogPrice\GroupPrice::PRICE_CODE, $this->quantity, $groupPrice],
                [CatalogPrice\SpecialPrice::PRICE_CODE, $this->quantity, $specialPrice],
            ]));

        $this->assertEquals($result, $this->model->getValue());
        $this->assertEquals($result, $this->model->getValue());
    }
}
