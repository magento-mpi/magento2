<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Pricing\Adjustment;

use Magento\Bundle\Pricing\Price;
use Magento\Bundle\Model\Product\Price as ProductPrice;

/**
 * Test for \Magento\Bundle\Pricing\Adjustment\Calculator
 */
class CalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItem;

    /**
     * @var \Magento\Framework\Pricing\Price\PriceInterface[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $priceMocks = [];

    /**
     * @var float
     */
    protected $baseAmount = 50.;

    /**
     * @var \Magento\Framework\Pricing\Adjustment\Calculator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $baseCalculator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectionFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxData;

    /**
     * @var Calculator
     */
    protected $model;

    protected function setUp()
    {
        $this->saleableItem = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getPriceInfo', 'getPriceType', '__wakeup', 'getStore'])
            ->disableOriginalConstructor()
            ->getMock();
        $priceInfo = $this->getMock('Magento\Framework\Pricing\PriceInfo\Base', [], [], '', false);
        $priceInfo->expects($this->any())->method('getPrice')->will($this->returnCallback(function ($type) {
            if (!isset($this->priceMocks[$type])) {
                throw new \PHPUnit_Framework_ExpectationFailedException('Unexpected type of price model');
            }
            return $this->priceMocks[$type];
        }));
        $this->saleableItem->expects($this->any())->method('getPriceInfo')->will($this->returnValue($priceInfo));

        $store = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $store->expects($this->any())->method('roundPrice')->will($this->returnArgument(0));

        $this->saleableItem->expects($this->any())->method('getStore')->will($this->returnValue($store));

        $this->baseCalculator = $this->getMock('Magento\Framework\Pricing\Adjustment\Calculator', [], [], '', false);
        $this->amountFactory = $this->getMock('Magento\Framework\Pricing\Amount\AmountFactory', [], [], '', false);
        $this->selectionFactory = $this->getMockBuilder('Magento\Bundle\Pricing\Price\BundleSelectionFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factoryCallback = $this->returnCallback(function () {
            list(, $selectionMock) = func_get_args();
            $bundlePrice = $this->getMockBuilder('Magento\Bundle\Pricing\Price\BundleSelectionPrice')
                ->setMethods(['getAmount'])
                ->disableOriginalConstructor()
                ->getMock();
            $bundlePrice->expects($this->any())->method('getAmount')
                ->will($this->returnValue($selectionMock->getAmountMock()));
            return $bundlePrice;
        });
        $this->selectionFactory->expects($this->any())->method('create')->will($factoryCallback);

        $this->taxData = $this->getMockBuilder('Magento\Tax\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new Calculator(
            $this->baseCalculator,
            $this->amountFactory,
            $this->selectionFactory,
            $this->taxData
        );
    }

    protected function tearDown()
    {
        $this->priceMocks = [];
    }

    /**
     * @dataProvider dataProviderForGetterAmount
     */
    public function testGetterAmount($amountForBundle, $optionList, $expectedResult)
    {
        $this->baseCalculator->expects($this->atLeastOnce())->method('getAmount')
            ->with($this->baseAmount, $this->saleableItem)
            ->will($this->returnValue($this->createAmountMock($amountForBundle)));

        $options = [];
        foreach ($optionList as $optionData) {
            $options[] = $this->createOptionMock($optionData);
        }
        $price = $this->getMock('Magento\Bundle\Pricing\Price\BundleOptionPrice', [], [], '', false);
        $price->expects($this->atLeastOnce())->method('getOptions')->will($this->returnValue($options));
        $this->priceMocks[Price\BundleOptionPrice::PRICE_CODE] = $price;

        // Price type of saleable items
        $this->saleableItem->expects($this->any())->method('getPriceType')->will($this->returnValue(
            ProductPrice::PRICE_TYPE_DYNAMIC
        ));

        $this->amountFactory->expects($this->atLeastOnce())->method('create')
            ->with($expectedResult['fullAmount'], $expectedResult['adjustments']);
        if ($expectedResult['isMinAmount']) {
            $this->model->getAmount($this->baseAmount, $this->saleableItem);
        } else {
            $this->model->getMaxAmount($this->baseAmount, $this->saleableItem);
        }
    }

    /**
     * Create amount mock
     *
     * @param array $amountData
     * @return \Magento\Framework\Pricing\Amount\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createAmountMock($amountData)
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Pricing\Amount\Base $amount */
        $amount = $this->getMock('Magento\Framework\Pricing\Amount\Base', [], [], '', false);
        $amount->expects($this->any())->method('getAdjustmentAmounts')
            ->will($this->returnValue($amountData['adjustmentsAmounts']));
        $amount->expects($this->any())->method('getValue')->will($this->returnValue($amountData['amount']));
        return $amount;
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
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product $selection */
        $selection = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['isSalable', '__wakeup', 'getAmountMock'])
            ->disableOriginalConstructor()
            ->getMock();
        // All items are saleable
        $selection->expects($this->any())->method('isSalable')->will($this->returnValue(true));
        $selection->setData($selectionData['data']);
        // Virtual method to bind a creation of amount mock through factory
        $amountMock = $this->createAmountMock($selectionData['amount']);
        $selection->expects($this->any())->method('getAmountMock')->will($this->returnValue($amountMock));
        return $selection;
    }

    /**
     * @return array
     */
    public function dataProviderForGetterAmount()
    {
        return [
            // first case with minimal amount
            'case with getting minimal amount' => $this->getCaseWithMinAmount(),

            // second case with maximum amount
            'case with getting maximum amount' => $this->getCaseWithMaxAmount(),

            // third case without saleable items
            'case without saleable items' => $this->getCaseWithoutSaleableItems(),

            // fourth case without require options
            'case without required options' => $this->getCaseMinAmountWithoutRequiredOptions(),
        ];
    }

    /**
     * Array for data provider dataProviderForGetterAmount for case 'case with getting minimal amount'
     *
     * @return array
     */
    protected function getCaseWithMinAmount()
    {
        return [
            'amountForBundle' => [
                'adjustmentsAmounts' => ['tax' => 102],
                'amount' => 782
            ],
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
                        'first product selection'  => [
                            'data'   => ['price' => 70.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 8, 'weee' => 10],
                                'amount' => 18
                            ]
                        ],
                        'second product selection' => [
                            'data'   => ['price' => 80.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 18],
                                'amount' => 28
                            ]
                        ],
                        'third product selection with the lowest price'  => [
                            'data'   => ['price' => 50.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 8, 'weee' => 10],
                                'amount' => 8
                            ]
                        ]
                    ]
                ],
            ],
            'expectedResult' => [
                'isMinAmount' => true,
                'fullAmount'  => 790.,
                'adjustments' => ['tax' => 110, 'weee' => 10]
            ]
        ];
    }

    /**
     * Array for data provider dataProviderForGetterAmount for case 'case with getting maximum amount'
     *
     * @return array
     */
    protected function getCaseWithMaxAmount()
    {
        return [
            'amountForBundle' => [
                'adjustmentsAmounts' => ['tax' => 102],
                'amount' => 782
            ],
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
                        'first product selection'  => [
                            'data'   => ['price' => 50.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 8, 'weee' => 10],
                                'amount' => 8
                            ]
                        ],
                        'second product selection' => [
                            'data'   => ['price' => 80.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 18],
                                'amount' => 8
                            ]
                        ]
                    ]
                ],
                // second option with multiselection
                [
                    'isMultiSelection' => true,
                    'data' => [
                        'title'         => 'test option 2',
                        'default_title' => 'test option 2',
                        'type'          => 'select',
                        'option_id'     => '2',
                        'position'      => '1',
                        'required'      => '1',
                    ],
                    'selections' => [
                        'first product selection'  => [
                            'data'   => ['price' => 20.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 8],
                                'amount' => 8
                            ]
                        ],
                        'second product selection' => [
                            'data'   => ['price' => 110.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 28],
                                'amount' => 28
                            ]
                        ],
                        'third product selection' => [
                            'data'   => ['price' => 50.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 18],
                                'amount' => 18
                            ]
                        ],
                    ]
                ]
            ],
            'expectedResult' => [
                'isMinAmount' => false,
                'fullAmount'  => 844.,
                'adjustments' => ['tax' => 164, 'weee' => 10]
            ]
        ];
    }

    /**
     * Array for data provider dataProviderForGetterAmount for case 'case without saleable items'
     *
     * @return array
     */
    protected function getCaseWithoutSaleableItems()
    {
        return [
            'amountForBundle' => [
                'adjustmentsAmounts' => ['tax' => 102],
                'amount' => 782
            ],
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
                    'selections' => []
                ],
            ],
            'expectedResult' => [
                'isMinAmount' => true,
                'fullAmount'  => 782.,
                'adjustments' => ['tax' => 102]
            ]
        ];
    }

    /**
     * Array for data provider dataProviderForGetterAmount for case 'case without required options'
     *
     * @return array
     */
    protected function getCaseMinAmountWithoutRequiredOptions()
    {
        return [
            'amountForBundle' => [
                'adjustmentsAmounts' => [],
                'amount' => null
            ],
            'optionList' => [
                // first option
                [
                    'isMultiSelection' => false,
                    'data' => [
                        'title'         => 'test option 1',
                        'default_title' => 'test option 1',
                        'type'          => 'select',
                        'option_id'     => '1',
                        'position'      => '0',
                        'required'      => '0',
                    ],
                    'selections' => [
                        'first product selection'  => [
                            'data'   => ['price' => 20.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 8],
                                'amount' => 8
                            ]
                        ],
                        'second product selection'  => [
                            'data'   => ['price' => 30.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 10],
                                'amount' => 12
                            ]
                        ],
                    ]
                ],
                // second option
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
                        'first product selection'  => [
                            'data'   => ['price' => 25.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 8],
                                'amount' => 9
                            ]
                        ],
                        'second product selection'  => [
                            'data'   => ['price' => 35.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 10],
                                'amount' => 10
                            ]
                        ],
                    ]
                ]
            ],
            'expectedResult' => [
                'isMinAmount' => true,
                'fullAmount'  => 8.,
                'adjustments' => ['tax' => 8]
            ]
        ];
    }
}
