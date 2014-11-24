<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Pricing\Price as CatalogPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;

/**
 * Class BundleSelectionPriceTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BundleSelectionPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Pricing\Price\BundleSelectionPrice
     */
    protected $selectionPrice;

    /**
     * @var \Magento\Framework\Pricing\Adjustment\CalculatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculatorMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $bundleMock;

    /**
     * @var \Magento\Framework\Event\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \Magento\Framework\Pricing\PriceInfo\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\Catalog\Pricing\Price\FinalPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $finalPriceMock;

    /**
     * @var \Magento\Catalog\Pricing\Price\RegularPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $regularPriceMock;

    /**
     * @var \Magento\Bundle\Pricing\Price\DiscountCalculator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $discountCalculatorMock;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * Test setUp
     */
    protected function setUp()
    {
        $this->productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['__wakeup', 'getPriceInfo', 'getSelectionPriceType', 'getSelectionPriceValue'],
            [],
            '',
            false
        );

        $this->bundleMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['__wakeup', 'getPriceType', 'getPriceInfo', 'setFinalPrice', 'getData'],
            [],
            '',
            false
        );
        $this->calculatorMock = $this->getMock(
            'Magento\Framework\Pricing\Adjustment\CalculatorInterface',
            [],
            [],
            '',
            false,
            true,
            false
        );
        $this->eventManagerMock = $this->getMock(
            'Magento\Framework\Event\Manager',
            ['dispatch'],
            [],
            '',
            false
        );
        $this->priceInfoMock = $this->getMock(
            'Magento\Framework\Pricing\PriceInfo\Base',
            ['getPrice'],
            [],
            '',
            false
        );
        $this->discountCalculatorMock = $this->getMock(
            'Magento\Bundle\Pricing\Price\DiscountCalculator',
            [],
            [],
            '',
            false
        );
        $this->finalPriceMock = $this->getMock(
            'Magento\Catalog\Pricing\Price\FinalPrice',
            [],
            [],
            '',
            false
        );
        $this->regularPriceMock = $this->getMock(
            'Magento\Catalog\Pricing\Price\RegularPrice',
            [],
            [],
            '',
            false
        );
        $this->productMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->quantity = 1;
    }

    protected function setupSelectionPrice($useRegularPrice = false)
    {
        $this->selectionPrice = new \Magento\Bundle\Pricing\Price\BundleSelectionPrice(
            $this->productMock,
            $this->quantity,
            $this->calculatorMock,
            $this->bundleMock,
            $this->eventManagerMock,
            $this->discountCalculatorMock,
            $useRegularPrice
        );
    }

    /**
     *  test fro method getValue with dynamic productType
     *
     * @param bool $useRegularPrice
     * @dataProvider useRegularPriceDataProvider
     */
    public function testGetValueTypeDynamic($useRegularPrice)
    {
        $this->setupSelectionPrice($useRegularPrice);
        $priceCode = $useRegularPrice ? RegularPrice::PRICE_CODE : FinalPrice::PRICE_CODE;
        $regularPrice = 100;
        $discountedPrice = 70;
        $expectedPrice = $useRegularPrice ? $regularPrice : $discountedPrice;

        $this->bundleMock->expects($this->once())
            ->method('getPriceType')
            ->will($this->returnValue(\Magento\Bundle\Model\Product\Price::PRICE_TYPE_DYNAMIC));
        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo($priceCode))
            ->will($this->returnValue($this->finalPriceMock));
        $this->finalPriceMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($regularPrice));

        if (!$useRegularPrice) {
            $this->discountCalculatorMock->expects($this->once())
                ->method('calculateDiscount')
                ->with(
                    $this->equalTo($this->bundleMock),
                    $this->equalTo($regularPrice)
                )
                ->will($this->returnValue($discountedPrice));
        }
        $this->assertEquals($expectedPrice, $this->selectionPrice->getValue());
        $this->assertEquals($expectedPrice, $this->selectionPrice->getValue());
    }

    /**
     * test for method getValue with type Fixed and selectionPriceType not null
     *
     * @param bool $useRegularPrice
     * @dataProvider useRegularPriceDataProvider
     */
    public function testGetValueTypeFixedWithSelectionPriceType($useRegularPrice)
    {
        $this->setupSelectionPrice($useRegularPrice);
        $regularPrice = 100;
        $discountedPrice = 70;
        $expectedPrice = $useRegularPrice ? $regularPrice : $discountedPrice;

        $this->bundleMock->expects($this->once())
            ->method('getPriceType')
            ->will($this->returnValue(\Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED));
        $this->bundleMock->expects($this->atLeastOnce())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));
        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo(RegularPrice::PRICE_CODE))
            ->will($this->returnValue($this->regularPriceMock));
        $this->regularPriceMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($regularPrice));
        $this->bundleMock->expects($this->once())
            ->method('setFinalPrice')
            ->will($this->returnSelf());
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch');
        $this->bundleMock->expects($this->exactly(2))
            ->method('getData')
            ->will($this->returnValueMap(
                    [
                        ['qty', null, 1],
                        ['final_price', null, 100],
                    ]
                )
            );
        $this->productMock->expects($this->once())
            ->method('getSelectionPriceType')
            ->will($this->returnValue(true));
        $this->productMock->expects($this->any())
            ->method('getSelectionPriceValue')
            ->will($this->returnValue($regularPrice));

        if (!$useRegularPrice) {
            $this->discountCalculatorMock->expects($this->once())
                ->method('calculateDiscount')
                ->with(
                    $this->equalTo($this->bundleMock),
                    $this->equalTo($regularPrice)
                )
                ->will($this->returnValue($discountedPrice));
        }
        $this->assertEquals($expectedPrice, $this->selectionPrice->getValue());
    }

    /**
     * test for method getValue with type Fixed and selectionPriceType is empty or zero
     *
     * @param bool $useRegularPrice
     * @dataProvider useRegularPriceDataProvider
     */
    public function testGetValueTypeFixedWithoutSelectionPriceType($useRegularPrice)
    {
        $this->setupSelectionPrice($useRegularPrice);
        $regularPrice = 100;
        $discountedPrice = 70;
        $expectedPrice = $useRegularPrice ? $regularPrice : $discountedPrice;

        $this->bundleMock->expects($this->once())
            ->method('getPriceType')
            ->will($this->returnValue(\Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED));
        $this->productMock->expects($this->once())
            ->method('getSelectionPriceType')
            ->will($this->returnValue(false));
        $this->productMock->expects($this->any())
            ->method('getSelectionPriceValue')
            ->will($this->returnValue(100));

        if (!$useRegularPrice) {
            $this->discountCalculatorMock->expects($this->once())
                ->method('calculateDiscount')
                ->with(
                    $this->equalTo($this->bundleMock),
                    $this->equalTo($regularPrice)
                )
                ->will($this->returnValue($discountedPrice));
        }
        $this->assertEquals($expectedPrice, $this->selectionPrice->getValue());
    }

    public function useRegularPriceDataProvider()
    {
        return [
            'useRegularPrice' => [
                true
            ],
            'notUseRegularPrice' => [
                false
            ],
        ];
    }
}
