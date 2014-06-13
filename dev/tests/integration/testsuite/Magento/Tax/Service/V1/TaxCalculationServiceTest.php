<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\TestFramework\Helper\Bootstrap;

class TaxCalculationServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * Tax calculation service
     *
     * @var \Magento\Tax\Service\V1\TaxCalculationService
     */
    private $taxCalculationService;

    /**
     * Tax Details Builder
     *
     * @var \Magento\Tax\Service\V1\Data\QuoteDetailsBuilder
     */
    private $quoteDetailsBuilder;

    /**
     * Tax Details Item Builder
     *
     * @var \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder
     */
    private $quoteDetailsItemBuilder;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->quoteDetailsBuilder = $this->objectManager
            ->create('Magento\Tax\Service\V1\Data\QuoteDetailsBuilder');
        $this->quoteDetailsItemBuilder = $this->objectManager
            ->create('Magento\Tax\Service\V1\data\QuoteDetails\ItemBuilder');
        $this->taxCalculationService = $this->objectManager->get('\Magento\Tax\Service\V1\TaxCalculationService');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testCalculateTaxUnitBased()
    {

    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     * @dataProvider calculateTaxRowBasedDataProvider
     */
    public function testCalculateTaxRowBased($quoteDetailsData, $expectedTaxDetails)
    {
        $quoteDetails = $this->quoteDetailsBuilder->populateWithArray($quoteDetailsData)->create();

        $taxDetails = $this->taxCalculationService->calculateTax($quoteDetails, 1);

        $this->assertEquals($expectedTaxDetails, $taxDetails->__toArray());
    }

    public function calculateTaxRowBasedDataProvider()
    {
        return [
            'one product' => [
                'quote_details' => [
                    'shipping_address' => [
                        'vat_id' => 0,
                        'postcode' => '55555',
                    ],
                    'items' => [
                        [
                            'code' => 'code',
                            'type' => 'type',
                            'quantity' => 1,
                            'unit_price' => 10,
                        ],
                    ],
                    'customer_tax_class_id' => 1
                ],
                'expected_tax_details' => [
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'taxable_amount' => 0,
                    'discount_amount' => 0,
                    'items' => [
                        [
                            'tax_amount' => 0,
                            'price' => 10,
                            'price_incl_tax' => 0,
                            'row_total' => 0,
                            'row_total_incl_tax' => 0,
                            'taxable_amount' => 0,
                            'code' => 'code',
                            'type' => 'type',
                            'tax_percent' => 0,
                        ],
                    ],
                ],
            ],
        ];
    }
}
