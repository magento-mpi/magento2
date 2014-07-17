<?php
/**
 * Created by PhpStorm.
 * User: bimathew
 * Date: 7/17/14
 * Time: 11:56 AM
 */

namespace Magento\Tax\Model\Calculation;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder;

class RowBaseAndTotalBaseCalculatorTestHelper
{
    const STORE_ID = 2300;
    const QUANTITY = 1;
    const UNIT_PRICE = 500;
    const RATE = 10;
    const STORE_RATE = 11;

    const CODE = 'CODE';
    const TYPE = 'TYPE';

    /** @var objectManager */
    protected $objectManager;

    /** @var \Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockTaxItemDetailsBuilder;

    /** @var \Magento\Tax\Model\Calculation | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockCalculationTool;

    /** @var \Magento\Tax\Model\Config | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockConfig;

    /** @var $mockItem \Magento\Tax\Service\V1\Data\QuoteDetails\Item | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockItem;

    /** @var $mockAppliedTaxBuilder AppliedTaxBuilder | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockAppliedTaxBuilder;

    /** @var $mockAppliedTaxRateBuilder AppliedTaxBuilder | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockAppliedTaxRateBuilder;

    /** @var \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockAppliedTax;

    public function __construct(
        $objectManager,
        $mockTaxItemDetailsBuilder,
        $mockCalculationTool,
        $mockConfig,
        $mockItem,
        $mockAppliedTaxBuilder,
        $mockAppliedTaxRateBuilder,
        $mockAppliedTax
    ) {

        $this->objectManager = $objectManager;
        $this->mockTaxItemDetailsBuilder = $mockTaxItemDetailsBuilder;
        $this->mockCalculationTool = $mockCalculationTool;
        $this->mockConfig = $mockConfig;
        $this->mockItem = $mockItem;
        $this->mockAppliedTaxBuilder = $mockAppliedTaxBuilder;
        $this->mockAppliedTaxRateBuilder = $mockAppliedTaxRateBuilder;
        $this->mockAppliedTax = $mockAppliedTax;
    }

    /**
     * @param bool $taxIncluded
     */
    public function getMocks($taxIncluded)
    {
        $this->getMockItem($taxIncluded);
        $this->getMockConfig();
        $this->getMockCalculationTool();
        $this->getMockItemBuilder();
        $this->getMockAppliedTaxBuilder();
    }

    /**
     * @param $calculator RowBaseCalculator|TotalBaseCalculator
     * @return \Magento\Tax\Service\V1\Data\QuoteDetails\Item
     */
    public function calculate($calculator)
    {
        return $calculator->calculate($this->mockItem, 1);
    }

    /**
     * @param bool $taxIncluded
     */
    protected function getMockItem($taxIncluded)
    {
        $this->objectManager->mockReturnValues(
            $this->mockItem,
            [
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'getDiscountAmount',
                    ObjectManager::MOCK_VALUE => 1
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'getCode',
                    ObjectManager::MOCK_VALUE => self::CODE
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'getType',
                    ObjectManager::MOCK_VALUE => self::TYPE
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'getUnitPrice',
                    ObjectManager::MOCK_VALUE => self::UNIT_PRICE
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'getTaxIncluded',
                    ObjectManager::MOCK_VALUE => $taxIncluded
                ]
            ]
        );
    }

    /**
     * Sets mock config
     *
     */
    protected function getMockConfig()
    {
        $this->objectManager->mockReturnValues(
            $this->mockConfig,
            [
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'applyTaxAfterDiscount',
                    ObjectManager::MOCK_VALUE => true
                ]
            ]
        );
    }

    /**
     * Sets mock calculation model
     *
     */
    protected function getMockCalculationTool()
    {
        $this->objectManager->mockReturnValues(
            $this->mockCalculationTool,
            [
                [
                    ObjectManager::ONCE => false,
                    ObjectManager::MOCK_METHOD_NAME => 'calcTaxAmount',
                    ObjectManager::MOCK_VALUE => 1.5
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'getRate',
                    ObjectManager::MOCK_VALUE => self::RATE
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'getAppliedRates',
                    ObjectManager::MOCK_VALUE => [
                        [
                            'id' => 0,
                            'percent' => 1.4,
                            'rates' => [
                                [
                                    'code' => 'sku_1',
                                    'title' => 'title1',
                                    'percent' => 1.1
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    ObjectManager::ONCE => false,
                    ObjectManager::MOCK_METHOD_NAME => 'round',
                    ObjectManager::MOCK_VALUE => 1.3
                ]
            ]
        );
    }

    /**
     * Sets mock taxItemBuilder
     *
     */

    protected function getMockItemBuilder()
    {
        $this->objectManager->mockReturnValues(
            $this->mockTaxItemDetailsBuilder,
            [
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'setType',
                    ObjectManager::MOCK_VALUE => self::TYPE
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'setCode',
                    ObjectManager::MOCK_VALUE => self::CODE
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'setRowTax',
                    ObjectManager::MOCK_VALUE => 1.3
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'setTaxPercent',
                    ObjectManager::MOCK_VALUE => self::RATE
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'create',
                    ObjectManager::MOCK_VALUE => 'SOME RETURN OBJECT'
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'getAppliedTaxBuilder',
                    ObjectManager::MOCK_VALUE => $this->mockAppliedTaxBuilder
                ]
            ]

        );
    }

    protected function getMockAppliedTaxBuilder()
    {

        $this->objectManager->mockReturnValues(
            $this->mockAppliedTaxBuilder,
            [
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'getAppliedTaxRateBuilder',
                    ObjectManager::MOCK_VALUE => $this->mockAppliedTaxRateBuilder
                ],
                [
                    ObjectManager::ONCE => true,
                    ObjectManager::MOCK_METHOD_NAME => 'create',
                    ObjectManager::MOCK_VALUE => $this->mockAppliedTax
                ]
            ]
        );
    }
}
