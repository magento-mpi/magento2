<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class BundleOptionPriceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Bundle\Pricing\Price\BundleOptionPrice */
    protected $bundleOptionPrice;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $saleableItemMock;

    /** @var \Magento\Bundle\Pricing\Adjustment\BundleCalculatorInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $bundleCalculatorMock;

    /** @var \Magento\Bundle\Pricing\Price\BundleSelectionFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $selectionFactoryMock;

    /** @var \Magento\Pricing\PriceInfo\Base|\PHPUnit_Framework_MockObject_MockObject */
    protected $priceInfoMock;

    protected function setUp()
    {
        $this->priceInfoMock = $this->getMock('Magento\Pricing\PriceInfo\Base', [], [], '', false);
        $this->saleableItemMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->saleableItemMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->saleableItemMock->expects($this->once())
            ->method('setQty')
            ->will($this->returnSelf());

        $this->bundleCalculatorMock = $this->getMock('Magento\Bundle\Pricing\Adjustment\BundleCalculatorInterface');
        $this->selectionFactoryMock = $this->getMock(
            'Magento\Bundle\Pricing\Price\BundleSelectionFactory',
            [],
            [],
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->bundleOptionPrice = $this->objectManagerHelper->getObject(
            'Magento\Bundle\Pricing\Price\BundleOptionPrice',
            [
                'salableItem' => $this->saleableItemMock,
                'quantity' => 1.,
                'calculator' => $this->bundleCalculatorMock,
                'bundleSelectionFactory' => $this->selectionFactoryMock
            ]
        );
    }

    /**
     * @dataProvider getOptionsDataProvider
     */
    public function testGetOptions($selectionCollection)
    {
        $this->prepareOptionMocks($selectionCollection);
        $this->assertSame($selectionCollection, $this->bundleOptionPrice->getOptions());
    }

    /**
     * @param array $selectionCollection
     * @return void
     */
    protected function prepareOptionMocks($selectionCollection)
    {
        $this->saleableItemMock->expects($this->once())
            ->method('getStoreId')
            ->will($this->returnValue(1));

        $priceTypeMock = $this->getMock('Magento\Bundle\Model\Product\Type', [], [], '', false);
        $priceTypeMock->expects($this->once())
            ->method('setStoreFilter')
            ->with($this->equalTo(1), $this->equalTo($this->saleableItemMock))
            ->will($this->returnSelf());

        $optionIds = ['41', '55'];
        $priceTypeMock->expects($this->once())
            ->method('getOptionsIds')
            ->with($this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($optionIds));

        $priceTypeMock->expects($this->once())
            ->method('getSelectionsCollection')
            ->with($this->equalTo($optionIds), $this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($selectionCollection));

        $collection = $this->getMock('Magento\Bundle\Model\Resource\Option\Collection', [], [], '', false);
        $collection->expects($this->once())
            ->method('appendSelections')
            ->with($this->equalTo($selectionCollection), $this->equalTo(false), $this->equalTo(false))
            ->will($this->returnValue($selectionCollection));

        $priceTypeMock->expects($this->once())
            ->method('getOptionsCollection')
            ->with($this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($collection));

        $this->saleableItemMock->expects($this->atLeastOnce())
            ->method('getTypeInstance')
            ->will($this->returnValue($priceTypeMock));
    }

    public function getOptionsDataProvider()
    {
        return [
            ['1', '2']
        ];
    }

    /**
     * @param float $selectionQty
     * @param float|bool $selectionAmount
     * @dataProvider selectionAmountDataProvider
     */
    public function testGetOptionSelectionAmount($selectionQty, $selectionAmount)
    {
        $selection = $this->getMock('Magento\Catalog\Model\Product', ['getSelectionQty', '__wakeup'], [], '', false);
        $selection->expects($this->once())
            ->method('getSelectionQty')
            ->will($this->returnValue($selectionQty));
        $priceMock = $this->getMock('Magento\Bundle\Pricing\Price\BundleSelectionPrice', [], [], '', false);
        $priceMock->expects($this->once())
            ->method('getAmount')
            ->will($this->returnValue($selectionAmount));
        $this->selectionFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo($this->saleableItemMock), $this->equalTo($selection), $this->equalTo($selectionQty))
            ->will($this->returnValue($priceMock));
        $this->assertSame($selectionAmount, $this->bundleOptionPrice->getOptionSelectionAmount($selection));
    }

    /**
     * @return array
     */
    public function selectionAmountDataProvider()
    {
        return [
            [1., 50.5],
            [2.2, false]
        ];
    }
}
