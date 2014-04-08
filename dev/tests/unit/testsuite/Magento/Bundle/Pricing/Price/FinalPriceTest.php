<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class FinalPriceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Bundle\Pricing\Price\FinalPrice */
    protected $finalPrice;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $saleableInterfaceMock;

    /** @var float */
    protected $quantity = 1.;

    /** @var float*/
    protected $baseAmount;

    /** @var float*/
    protected $maxValue;

    /** @var \Magento\Bundle\Pricing\Adjustment\BundleCalculatorInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $bundleCalculatorMock;

    /** @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $priceInfoMock;

    /** @var \Magento\Bundle\Pricing\Price\BasePrice|\PHPUnit_Framework_MockObject_MockObject */
    protected $basePriceMock;

    /** @var BundleOptionPrice|\PHPUnit_Framework_MockObject_MockObject */
    protected $bundleOptionMock;

    protected function prepareMock()
    {
        $this->saleableInterfaceMock = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $this->bundleCalculatorMock = $this->getMock('Magento\Bundle\Pricing\Adjustment\BundleCalculatorInterface');
        $this->bundleOptionMock = $this->getMock('Magento\Bundle\Pricing\Price\BundleOptionPrice', [], [], '', false);
        $this->basePriceMock = $this->getMock('Magento\Bundle\Pricing\Price\BasePrice', [], [], '', false);
        $this->basePriceMock->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue($this->baseAmount));

        $this->basePriceMock->expects($this->any())
            ->method('getMaxValue')
            ->will($this->returnValue($this->maxValue));

        $this->priceInfoMock = $this->getMock('\Magento\Pricing\PriceInfoInterface');
        $this->priceInfoMock->expects($this->atLeastOnce())
            ->method('getPrice')
            ->will($this->returnCallback(array($this, 'getPriceCallback')));

        $this->saleableInterfaceMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->finalPrice = $this->objectManagerHelper->getObject(
            'Magento\Bundle\Pricing\Price\FinalPrice',
            [
                'salableItem' => $this->saleableInterfaceMock,
                'quantity' => $this->quantity,
                'calculator' => $this->bundleCalculatorMock
            ]
        );
    }

    /**
     * @param $priceType
     * @return BasePrice|BundleOptionPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getPriceCallback($priceType)
    {
        switch ($priceType) {
            case \Magento\Catalog\Pricing\Price\BasePrice::PRICE_TYPE_BASE_PRICE:
                return $this->basePriceMock;
            case BundleOptionPriceInterface::PRICE_TYPE_BUNDLE_OPTION:
                return $this->bundleOptionMock;
            default:
                break;
        }
        $this->fail('Price mock was not found');
    }

    /**
     * @dataProvider getValueDataProvider
     */
    public function testGetValue($baseAmount, $discountValue, $result)
    {
        $this->baseAmount = $baseAmount;
        $optionsValue = 2.0;
        $this->prepareMock();
        $this->bundleOptionMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($optionsValue));

        $this->basePriceMock->expects($this->once())->method('applyDiscount')
            ->with($this->equalTo($optionsValue))
            ->will($this->returnValue($discountValue));

        $this->assertSame($result, $this->finalPrice->getValue());
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        return [
            [false, false, 0],
            [0, 1.2, 1.2],
            [1, 2, 3]
        ];
    }

    /**
     * @dataProvider getValueDataProvider
     */
    public function testGetMaximalPrice($maxValue)
    {
        $result = 12;
        $this->maxValue = $maxValue;
        $this->prepareMock();

        $this->bundleCalculatorMock->expects($this->once())
            ->method('getMaxAmount')
            ->with($this->equalTo($this->maxValue), $this->equalTo($this->saleableInterfaceMock))
            ->will($this->returnValue($result));
        $this->assertSame($result, $this->finalPrice->getMaximalPrice());
    }

    /**
     * @dataProvider getValueDataProvider
     */
    public function testGetMinimalPrice($baseAmount)
    {
        $result = 12;
        $this->baseAmount = $baseAmount;
        $this->prepareMock();

        $this->bundleCalculatorMock->expects($this->once())
            ->method('getAmount')
            ->with($this->equalTo($this->baseAmount), $this->equalTo($this->saleableInterfaceMock))
            ->will($this->returnValue($result));
        $this->assertSame($result, $this->finalPrice->getMinimalPrice());
    }
}
