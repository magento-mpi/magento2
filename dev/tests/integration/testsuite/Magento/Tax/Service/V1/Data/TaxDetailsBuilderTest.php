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

    private static $travisCtyRateObjectDataArray = [
        'code' => '9',
        'title' => 'TX-TRAVIS',
        'percent' => 0.0825,
    ];

    private static $utopiaCtyRateObjectDataArray = [
        'code' => '2',
        'title' => 'TX-UTOPIA',
        'percent' => 0.01,
    ];

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

    public function testTaxDetailsNoTax()
    {
        $taxDetailsDataArray = [
            'subtotal' => 9.99,
            'tax_amount' => 0.00,
            'taxable_amount' => 0.00,
            'discount_amount' => 0.00,
            'applied_taxes' => null,
            'items' => null,
        ];

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

    public function testTaxDetailsSingleTax()
    {
        $appliedTaxDataArray = [
            'tax_rate_key' => '1',
            'percent' => 0.0825,
            'amount' => 1.65,
            'rates' => [
                self::$travisCtyRateObjectDataArray,
            ],
        ];

        $appliedTaxDataObjectFromArray = $this->appliedTaxBuilder
            ->populateWithArray($appliedTaxDataArray)
            ->create();
        $appliedTaxDataObjectFromObject = $this->appliedTaxBuilder
            ->populate($appliedTaxDataObjectFromArray)
            ->create();

        $this->assertEquals(
            $appliedTaxDataArray,
            $appliedTaxDataObjectFromArray->__toArray()
        );
        $this->assertEquals(
            $appliedTaxDataArray,
            $appliedTaxDataObjectFromObject->__toArray()
        );
        $this->assertEquals(
            $appliedTaxDataObjectFromArray,
            $appliedTaxDataObjectFromArray
        );

        $taxDetailsItemDataArray = [
            'code' => '123123',
            'type' => 'product',
            'tax_percent' => 0.0825,
            'price' => 19.99,
            'price_incl_tax' => 21.64,
            'row_total' => 19.99,
            'row_total_incl_tax' => 21.64,
            'tax_amount' => 1.65,
            'taxable_amount' => 19.99,
            'discount_amount' => 0.00,
            'discount_tax_compensation_amount' => 0.00,
            'applied_taxes' => [
                $appliedTaxDataArray,
            ],
        ];

        $taxDetailsItemDataObjectFromArray = $this->taxDetailsItemBuilder
            ->populateWithArray($taxDetailsItemDataArray)
            ->create();
        $taxDetailsItemDataObjectFromObject = $this->taxDetailsItemBuilder
            ->populate($taxDetailsItemDataObjectFromArray)
            ->create();

        $this->assertEquals(
            $taxDetailsItemDataArray,
            $taxDetailsItemDataObjectFromArray->__toArray()
        );
        $this->assertEquals(
            $taxDetailsItemDataArray,
            $taxDetailsItemDataObjectFromObject->__toArray()
        );
        $this->assertEquals(
            $taxDetailsItemDataObjectFromArray,
            $taxDetailsItemDataObjectFromObject
        );

        $taxDetailsDataArray = [
            'subtotal' => 19.99,
            'tax_amount' => 1.65,
            'taxable_amount' => 19.99,
            'discount_amount' => 0.00,
            'applied_taxes' => [
                $appliedTaxDataArray,
            ],
            'items' => [
                $taxDetailsItemDataArray,
            ],
        ];

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

    public function testTaxDetailsMultipleTax()
    {
        $appliedTaxDataArray = [
            'tax_rate_key' => '2',
            'percent' => 0.0925,
            'amount' => 1.85,
            'rates' => [
                self::$travisCtyRateObjectDataArray,
                self::$utopiaCtyRateObjectDataArray
            ],
        ];

        $taxDetailsItemDataArray = [
            'code' => '123123',
            'type' => 'product',
            'tax_percent' => 0.0925,
            'price' => 19.99,
            'price_incl_tax' => 21.84,
            'row_total' => 19.99,
            'row_total_incl_tax' => 21.84,
            'tax_amount' => 1.85,
            'taxable_amount' => 19.99,
            'discount_amount' => 0.00,
            'discount_tax_compensation_amount' => 0.00,
            'applied_taxes' => [
                $appliedTaxDataArray,
            ],
        ];

        $taxDetailsDataArray = [
            'subtotal' => 19.99,
            'tax_amount' => 1.85,
            'taxable_amount' => 19.99,
            'discount_amount' => 0.00,
            'applied_taxes' => [
                $appliedTaxDataArray,
            ],
            'items' => [
                $taxDetailsItemDataArray,
            ],
        ];

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
}
