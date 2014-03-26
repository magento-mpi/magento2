<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Pricing\Price;

/**
 * Base price test
 */
class BasePriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Pricing\Price\BasePrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $basePrice;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $salableItemMock;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * Prepare getBaseValue
     */
    protected function getBaseValue()
    {
        $this->salableItemMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getPriceInfo', '__wakeup'],
            [],
            '',
            false
        );
        $this->priceInfoMock = $this->getMock(
            '\Magento\Pricing\PriceInfo',
            ['getPriceComposite', 'getPrice', 'getAdjustments'],
            [],
            '',
            false
        );

        $finalPriceMock = $this->getMock(
            'Magento\Catalog\Pricing\Price\FinalPrice',
            ['getValue'],
            [],
            '',
            false,
            false
        );
        $groupPriceMock = $this->getMock(
            'Magento\Catalog\Pricing\Price\GroupPrice',
            ['getValue'],
            [],
            '',
            false,
            false
        );
        $specialPriceMock = $this->getMock(
            'Magento\Catalog\Pricing\Price\SpecialPrice',
            ['getValue'],
            [],
            '',
            false,
            false
        );

        $priceCodes = [
            'group' => 'group_price',
            'final' => 'final_price',
            'special' => 'special_price',
        ];
        $priceCompositeMock = $this->getMock('Magento\Pricing\PriceComposite', ['getPriceCodes'], [], '', false, false);
        $priceCompositeMock->expects($this->any())
            ->method('getPriceCodes')
            ->will($this->returnValue($priceCodes));

        $this->salableItemMock->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->priceInfoMock->expects($this->any())
            ->method('getPriceComposite')
            ->will($this->returnValue($priceCompositeMock));
        $this->priceInfoMock->expects($this->any())
            ->method('getAdjustments')
            ->will($this->returnValue([]));

        $groupPriceMock->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('43'));
        $specialPriceMock->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('34'));

        $priceValueMap = [
            [$priceCodes['group'], $groupPriceMock],
            [$priceCodes['final'], $finalPriceMock],
            [$priceCodes['special'], $specialPriceMock]
        ];
        $this->priceInfoMock->expects($this->any())
            ->method('getPrice')
            ->will($this->returnValueMap($priceValueMap));

        $this->basePrice = $this->objectManager->getObject(
            'Magento\Catalog\Pricing\Price\BasePrice',
            [
                'salableItem' => $this->salableItemMock,
                'priceType' => \Magento\Catalog\Pricing\Price\BasePrice::PRICE_TYPE_BASE_PRICE
            ]
        );
    }

    /**
     * Test case for BasePrice::getValue method
     */
    public function testGetValue()
    {
        $this->getBaseValue();
        $this->assertEquals(34, $this->basePrice->getValue());
    }

    /**
     * Test case for BasePrice::getMaxValue method
     */
    public function testGetMaxValue()
    {
        $this->getBaseValue();
        $this->assertEquals(43, $this->basePrice->getMaxValue());
    }
}