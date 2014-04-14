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
    /**
     * @var \Magento\Bundle\Pricing\Price\BundleOptionPrice
     */
    protected $bundleOptionPrice;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItemMock;

    /**
     * @var \Magento\Bundle\Pricing\Adjustment\BundleCalculatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $bundleCalculatorMock;

    /**
     * @var \Magento\Bundle\Pricing\Price\BundleSelectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectionFactoryMock;

    /**
     * @var \Magento\Pricing\PriceInfo\Base|\PHPUnit_Framework_MockObject_MockObject
     */
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

    public function testGetAmount()
    {
        $amountMock = $this->getMock('Magento\Pricing\Amount\AmountInterface');
        $this->bundleCalculatorMock->expects($this->once())
            ->method('getOptionsAmount')
            ->with($this->equalTo($this->saleableItemMock))
            ->will($this->returnValue($amountMock));
        $this->assertSame($amountMock, $this->bundleOptionPrice->getAmount());
    }

    /**
     * Create option mock
     *
     * @param array $optionData
     * @return \Magento\Bundle\Model\Option|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createOptionMock($optionData)
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Bundle\Model\Option $option */
        $option = $this->getMock('Magento\Bundle\Model\Option', ['isMultiSelection', '__wakeup'], [], '', false);
        $option->expects($this->any())->method('isMultiSelection')
            ->will($this->returnValue($optionData['isMultiSelection']));
        $selections = [];
        foreach ($optionData['selections'] as $selectionData) {
            $selections[] = $this->createSelectionMock($selectionData);
        }
        $option->setData($optionData['data']);
        $option->setData('selections', $selections);
        return $option;
    }

    /**
     * Create selection product mock
     *
     * @param array $selectionData
     * @return \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createSelectionMock($selectionData)
    {
        $selection = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['isSalable', 'getValue', 'getSelectionQty', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        // All items are saleable
        $selection->expects($this->any())->method('isSalable')->will($this->returnValue(true));
        $selection->expects($this->any())->method('getValue')->will($this->returnValue($selectionData['value']));
        return $selection;
    }

    /**
     * @dataProvider getTestDataForCalculation
     */
    public function testCalculation($optionList, $expected)
    {
        $storeId = 1;
        $this->saleableItemMock->expects($this->any())->method('getStoreId')->will($this->returnValue($storeId));

        $options = [];
        foreach ($optionList as $optionData) {
            $options[] = $this->createOptionMock($optionData);
        }
        /** @var \PHPUnit_Framework_MockObject_MockObject $optionsCollection */
        $optionsCollection = $this->getMock('Magento\Bundle\Model\Resource\Option\Collection', [], [], '', false);
        $optionsCollection->expects($this->once())->method('appendSelections')->will($this->returnSelf());
        $optionsCollection->expects($this->atLeastOnce())->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator($options)));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product\Type\AbstractType $typeMock */
        $typeMock = $this->getMock('Magento\Bundle\Model\Product\Type', [], [], '', false);
        $typeMock->expects($this->once())->method('setStoreFilter')->with($storeId, $this->saleableItemMock);
        $typeMock->expects($this->any())->method('getOptionsCollection')->with($this->saleableItemMock)
            ->will($this->returnValue($optionsCollection));
        $this->saleableItemMock->expects($this->any())->method('getTypeInstance')->will($this->returnValue($typeMock));
        $this->selectionFactoryMock->expects($this->any())->method('create')->will($this->returnArgument(1));

        $this->assertEquals($expected['min'], $this->bundleOptionPrice->getValue());
        $this->assertEquals($expected['max'], $this->bundleOptionPrice->getMaxValue());
    }

    /**
     * @return array
     */
    public function getTestDataForCalculation()
    {
        return [
            'first case' => [
                'optionList' => [
                    // first option with single choice of product
                    [
                        'isMultiSelection' => false,
                        'data' => [
                            'title'         => 'test option 1',
                            'default_title' => 'test option 1',
                            'type'          => 'select',
                            'option_id'     => '1',
                            'position'      => '0',
                            'required'      => '1',
                        ],
                        'selections' => [
                            ['value'=> 70.],
                            ['value' => 80.],
                            ['value' => 50.]
                        ]
                    ],
                    // second not required option
                    [
                        'isMultiSelection' => false,
                        'data' => [
                            'title'         => 'test option 2',
                            'default_title' => 'test option 2',
                            'type'          => 'select',
                            'option_id'     => '2',
                            'position'      => '1',
                            'required'      => '0',
                        ],
                        'selections' => [
                            ['value' => 20.]
                        ]
                    ],
                    // third with multiselection
                    [
                        'isMultiSelection' => true,
                        'data' => [
                            'title'         => 'test option 3',
                            'default_title' => 'test option 3',
                            'type'          => 'select',
                            'option_id'     => '3',
                            'position'      => '2',
                            'required'      => '1',
                        ],
                        'selections' => [
                            ['value' => 40.],
                            ['value' => 20.],
                            ['value' => 60.],
                        ]
                    ],
                    // fourth without selections
                    [
                        'isMultiSelection' => true,
                        'data' => [
                            'title'         => 'test option 3',
                            'default_title' => 'test option 3',
                            'type'          => 'select',
                            'option_id'     => '4',
                            'position'      => '3',
                            'required'      => '1',
                        ],
                        'selections' => []
                    ],
                ],
                'expected' => ['min' => 70, 'max' => 220]
            ]
        ];
    }
}
