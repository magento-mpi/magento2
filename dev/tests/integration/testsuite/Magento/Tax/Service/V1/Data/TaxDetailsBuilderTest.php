<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

class TaxDetailsBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * Applied Tax data object builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder
     */
    private $appliedTaxBuilder;

    /**
     * Tax Details Item data object builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder
     */
    private $taxDetailsItemBuilder;

    /**
     * Tax Details data object builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxDetailsBuilder
     */
    private $taxDetailsBuilder;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->appliedTaxBuilder = $this->objectManager
            ->create('\Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder');
        $this->taxDetailsItemBuilder = $this->objectManager
            ->create('\Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder');
        $this->taxDetailsBuilder = $this->objectManager
            ->create('\Magento\Tax\Service\V1\Data\TaxDetailsBuilder');
    }

    /**
     * @param array $taxDetailsDataArray
     *
     * @dataProvider taxDetailsPopulateDataProvider
     */
    public function testTaxDetailsPopulate($taxDetailsDataArray)
    {
        $taxDetailsDataObjectFromArray = $this->taxDetailsBuilder
            ->populateWithArray($taxDetailsDataArray)
            ->create();
        $taxDetailsDataObjectFromObject = $this->taxDetailsBuilder
            ->populate($taxDetailsDataObjectFromArray)
            ->create();

        $this->assertEquals(
            $taxDetailsDataArray,
            $taxDetailsDataObjectFromArray->__toArray()
        );
        $this->assertEquals(
            $taxDetailsDataArray,
            $taxDetailsDataObjectFromObject->__toArray()
        );
        $this->assertEquals(
            $taxDetailsDataObjectFromArray,
            $taxDetailsDataObjectFromObject
        );
    }

    public function taxDetailsPopulateDataProvider()
    {
        $appliedTaxDataArray = [
            'tax_rate_key' => '1',
            'percent' => 0.0825,
            'amount' => 1.65,
            'rates' => [
                [
                    'code' => '9',
                    'title' => 'TX-TRAVIS',
                    'percent' => 0.0825,
                ]
            ],
        ];

        $taxDetailsItemDataArray = [
            'code' => '123123',
            'type' => 'product',
            'tax_percent' => 0.0825,
            'price' => 19.99,
            'price_incl_tax' => 21.64,
            'row_total' => 19.99,
            'row_tax' => 1.65,
            'taxable_amount' => 19.99,
            'discount_amount' => 0.00,
            'discount_tax_compensation_amount' => 0.00,
            'applied_taxes' => [
                $appliedTaxDataArray,
            ],
        ];

        return [
            'no_items' => [[
                'subtotal' => 9.99,
                'tax_amount' => 0.00,
            ]],
            'single_item' => [[
                'subtotal' => 19.99,
                'tax_amount' => 1.65,
                'applied_taxes' => [
                    $appliedTaxDataArray,
                ],
                'items' => [
                    $taxDetailsItemDataArray,
                ],
            ]],
            'multiple_items' => [[
                'subtotal' => 19.99,
                'tax_amount' => 1.65,
                'applied_taxes' => [
                    $appliedTaxDataArray,
                    $appliedTaxDataArray,
                    $appliedTaxDataArray,
                ],
                'items' => [
                    $taxDetailsItemDataArray,
                    $taxDetailsItemDataArray,
                    $taxDetailsItemDataArray,
                ],
            ]],
        ];
    }
}
