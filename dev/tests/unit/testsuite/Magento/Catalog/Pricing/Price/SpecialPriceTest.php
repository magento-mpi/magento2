<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

class SpecialPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @param bool $isValidInterval
     * @param float $specialPrice
     * @param float|bool $expected
     *
     * @dataProvider specialPriceDataProvider
     */
    public function testGetValue($isValidInterval, $specialPrice, $expected)
    {
        $specialPriceMock = $this->objectManager->getObject(
            'Magento\Catalog\Pricing\Price\SpecialPrice',
            [
                'salableItem' => $this->prepareSalebleItem($specialPrice),
                'localeDate'  => $this->prepareLocaleDate($isValidInterval)
            ]
        );

        $this->assertSame($expected, $specialPriceMock->getValue());
    }

    /**
     * @param float $specialPrice
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product
     */
    protected function prepareSalebleItem($specialPrice)
    {
        $salableItemMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getSpecialPrice', 'getPriceInfo', 'getStore', '__wakeup'],
            [],
            '',
            false
        );

        $salableItemMock->expects($this->any())
            ->method('getSpecialPrice')
            ->will($this->returnValue($specialPrice));

        $priceInfo = $this->getMockBuilder(
            'Magento\Pricing\PriceInfoInterface'
        )->disableOriginalConstructor()->getMockForAbstractClass();

        $priceInfo->expects($this->any())
            ->method('getAdjustments')
            ->will($this->returnValue([]));

        $salableItemMock->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfo));

        return $salableItemMock;
    }

    /**
     * @param bool $isValidInterval
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Stdlib\DateTime\TimezoneInterface
     */
    protected function prepareLocaleDate($isValidInterval)
    {
        $localeDate = $this->getMockBuilder(
            'Magento\Stdlib\DateTime\TimezoneInterface'
        )->disableOriginalConstructor()->getMockForAbstractClass();

        $localeDate->expects($this->any())
            ->method('isScopeDateInInterval')
            ->will($this->returnValue($isValidInterval));

        return $localeDate;
    }

    /**
     * @return array
     */
    public function specialPriceDataProvider()
    {
        return [
            'validInterval' => [
                'is_valid_date' => true,
                'special_price' => 50.15,
                'expected'      => 50.15,
            ],
            'validZeroValue' => [
                'is_valid_date' => true,
                'special_price' => 0.,
                'expected'      => 0.,
            ],
            'invalidInterval' => [
                'is_valid_date' => false,
                'special_price' => 20.,
                'expected'      => false,
            ]
        ];
    }
}
