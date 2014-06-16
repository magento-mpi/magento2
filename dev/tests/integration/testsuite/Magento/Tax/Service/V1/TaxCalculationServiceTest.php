<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Tax\Model\ClassModel;
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

    /**
     * Array of default tax classes ids
     *
     * Key is class name
     *
     * @var int[]
     */
    private $taxClasses;

    /**
     * Array of default tax rates ids.
     *
     * Key is rate percentage as string.
     *
     * @var int[]
     */
    private $taxRates;

    /**
     * Array of default tax rules ids.
     *
     * Key is rule code.
     *
     * @var int[]
     */
    private $taxRules;

    /**
     * Helps in creating required tax rules.
     *
     * @var TaxRuleFixtureFactory
     */
    private $taxRuleFixtureFactory;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->quoteDetailsBuilder = $this->objectManager
            ->create('Magento\Tax\Service\V1\Data\QuoteDetailsBuilder');
        $this->quoteDetailsItemBuilder = $this->objectManager
            ->create('Magento\Tax\Service\V1\data\QuoteDetails\ItemBuilder');
        $this->taxCalculationService = $this->objectManager->get('\Magento\Tax\Service\V1\TaxCalculationService');
        $this->taxRuleFixtureFactory = new TaxRuleFixtureFactory();

        $this->setUpDefaultRules();
    }

    protected function tearDown()
    {
        $this->tearDownDefaultRules();
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
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     * @dataProvider calculateTaxDataProvider
     * @magentoConfigFixture current_store tax/calculation/algorithm TOTAL_BASE_CALCULATION
     */
    public function testCalculateTaxTotalBased($quoteDetailsData, $expectedTaxDetails, $storeId = null)
    {
        $quoteDetails = $this->quoteDetailsBuilder->populateWithArray($quoteDetailsData)->create();

        $taxDetails = $this->taxCalculationService->calculateTax($quoteDetails, $storeId);

        $this->assertEquals($expectedTaxDetails, $taxDetails->__toArray());
    }

    public function calculateTaxDataProvider()
    {
        $data = [
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
                        'unit_price' => 10.0,
                    ],
                ],
                'customer_tax_class_id' => 1
            ],
            'expected_tax_details' => [
                'subtotal' => 0.0,
                'tax_amount' => 0.0,
                'discount_amount' => 0.0,
                'items' => [
                    [
                        'tax_amount' => 0,
                        'price' => 10.0,
                        'price_incl_tax' => 0.0,
                        'row_total' => 0.0,
                        'row_total_incl_tax' => 0.0,
                        'taxable_amount' => 0.0,
                        'code' => 'code',
                        'type' => 'type',
                        'tax_percent' => 0,
                    ],
                ],
            ],
            'store_id' => null,
        ];

        $oneProductWithStoreIdWithTaxClassId = $data;
        $oneProductWithStoreIdWithoutTaxClassId = $data;
        $oneProductWithoutStoreIdWithoutTaxClassId = $data;

        $oneProductWithStoreIdWithTaxClassId['store_id'] = 1;
        $oneProductWithStoreIdWithoutTaxClassId['store_id'] = 1;

        $oneProductWithStoreIdWithTaxClassId['quote_details']['items'][0]['tax_class_id'] = 2;
        $oneProductWithoutStoreIdWithoutTaxClassId['quote_details']['items'][0]['tax_class_id'] = 2;

        return [
            'one product with store id, with tax class id' => $oneProductWithStoreIdWithTaxClassId,
            'one product with store id, without tax class id' => $oneProductWithStoreIdWithoutTaxClassId,
            'one product without store id, with tax class id' => $oneProductWithoutStoreIdWithoutTaxClassId,
            'one product without store id, without tax class id' => $data,
        ];
    }

    /**
     * @magentoDbIsolation enabled
     * @dataProvider calculateTaxRowBasedDataProvider
     * @magentoConfigFixture default_store tax/calculation/algorithm ROW_BASE_CALCULATION
     */
    public function testCalculateTaxRowBased($quoteDetailsData, $expectedTaxDetails)
    {
        $quoteDetailsData = $this->performTaxClassSubstitution($quoteDetailsData);

        $quoteDetails = $this->quoteDetailsBuilder->populateWithArray($quoteDetailsData)->create();

        $taxDetails = $this->taxCalculationService->calculateTax($quoteDetails);

        $this->assertEquals($expectedTaxDetails, $taxDetails->__toArray());
    }

    public function calculateTaxRowBasedDataProvider()
    {
        $baseQuote = [
            'shipping_address' => [
                'postcode' => '55555',
                'country_id' => 'US',
                'region' => ['region_id' => 42],
            ],
            'items' => [],
            'customer_tax_class_id' => 'DefaultCustomerClass',
        ];
        $oneProduct = $baseQuote;
        $oneProduct['items'][] = [
            'code' => 'sku_1',
            'type' => 'product',
            'quantity' => 10,
            'unit_price' => 1,
            'row_total' => 10,
            'tax_class_id' => 'DefaultProductClass',
        ];
        $oneProductResults = [
            'subtotal' => 10,
            'tax_amount' => 0.75,
            'discount_amount' => 0,
            'items' => [
                [
                    'price' => 1,
                    'price_incl_tax' => 1.08,
                    'row_total' => 10,
                    'row_total_incl_tax' => 10.75,
                    'taxable_amount' => 10,
                    'code' => 'sku_1',
                    'type' => 'product',
                    'tax_percent' => 7.5,
                    'tax_amount' => 0.75,
                ],
            ],
        ];

        $oneProductInclTax = $baseQuote;
        $oneProductInclTax['items'][] = [
            'code' => 'sku_1',
            'type' => 'product',
            'quantity' => 10,
            'unit_price' => 1.075,
            'row_total' => 10.75,
            'tax_class_id' => 'DefaultProductClass',
            'tax_included' => true,
        ];
        $oneProductInclTaxResults = $oneProductResults;
        // TODO: I think this is a bug, but the old code behaved this way so keeping it for now.
        $oneProductInclTaxResults['items'][0]['taxable_amount'] = 10.75;

        $oneProductInclTaxDiffRate = $baseQuote;
        $oneProductInclTaxDiffRate['items'][] = [
            'code' => 'sku_1',
            'type' => 'product',
            'quantity' => 10,
            'unit_price' => 1.1,
            'row_total' => 11,
            'tax_class_id' => 'HigherProductClass',
            'tax_included' => true,
        ];
        $oneProductInclTaxDiffRateResults = [
            'subtotal' => 10.0,
            'tax_amount' => 2.2,
            'discount_amount' => 0,
            'items' => [
                [
                    'price' => 1,
                    'price_incl_tax' => 1.22,
                    'row_total' => 10,
                    'row_total_incl_tax' => 12.2,
                    'taxable_amount' => 12.2, // TODO: Possible bug, shouldn't this be 10?
                    'code' => 'sku_1',
                    'type' => 'product',
                    'tax_percent' => 22.0,
                    'tax_amount' => 2.2,
                ],
            ],
        ];

        $twoProducts = $baseQuote;
        $twoProducts['items'] = [
            [
                'code' => 'sku_1',
                'type' => 'product',
                'quantity' => 10,
                'unit_price' => 1,
                'row_total' => 10,
                'tax_class_id' => 'DefaultProductClass',
            ],
            [
                'code' => 'sku_2',
                'type' => 'product',
                'quantity' => 20,
                'unit_price' => 11,
                'row_total' => 220,
                'tax_class_id' => 'DefaultProductClass',
            ]
        ];
        $twoProductsResults = [
            'subtotal' => 230,
            'tax_amount' => 17.25,
            'discount_amount' => 0,
            'items' => [
                [
                    'price' => 1,
                    'price_incl_tax' => 1.08,
                    'row_total' => 10,
                    'row_total_incl_tax' => 10.75,
                    'taxable_amount' => 10,
                    'code' => 'sku_1',
                    'type' => 'product',
                    'tax_percent' => 7.5,
                    'tax_amount' => .75,
                ],
                [
                    'price' => 11,
                    'price_incl_tax' => 11.83, // Unit price would have been 11.82 but row price is 11.83 (rounding)
                    'row_total' => 220,
                    'row_total_incl_tax' => 236.5,
                    'taxable_amount' => 220,
                    'code' => 'sku_2',
                    'type' => 'product',
                    'tax_percent' => 7.5,
                    'tax_amount' => 16.5,
                ],
            ],
        ];

        return [
            'one product' => [
                'quote_details' => $oneProduct,
                'expected_tax_details' => $oneProductResults,
            ],
            'one product tax included' => [
                'quote_details' => $oneProductInclTax,
                'expected_tax_details' => $oneProductInclTaxResults,
            ],
            'one product tax included but differs from store rate' => [
                'quote_details' => $oneProductInclTaxDiffRate,
                'expected_tax_details' => $oneProductInclTaxDiffRateResults,
            ],
            'two items, quantity three' => [
                'quote_details' => $twoProducts,
                'expected_tax_details' => $twoProductsResults,
            ],
        ];
    }

    private function performTaxClassSubstitution($data)
    {
        array_walk_recursive($data,
            function (&$value, $key) {
                if ( ($key === 'tax_class_id' || $key === 'customer_tax_class_id')
                    && is_string($value)
                ) {
                    $value = $this->taxClasses[$value];
                }
            }
        );

        return $data;
    }

    /**
     * Helper function that sets up some default rules
     */
    private function setUpDefaultRules()
    {
        $this->taxClasses = $this->taxRuleFixtureFactory->createTaxClasses([
            ['name' => 'DefaultCustomerClass', 'type' => ClassModel::TAX_CLASS_TYPE_CUSTOMER],
            ['name' => 'DefaultProductClass', 'type' => ClassModel::TAX_CLASS_TYPE_PRODUCT],
            ['name' => 'HigherProductClass', 'type' => ClassModel::TAX_CLASS_TYPE_PRODUCT],
        ]);

        $this->taxRates = $this->taxRuleFixtureFactory->createTaxRates([
            ['percentage' => 7.5, 'country' => 'US', 'region' => 42],
            ['percentage' => 7.5, 'country' => 'US', 'region' => 12], // Default store rate
        ]);

        $higherRates = $this->taxRuleFixtureFactory->createTaxRates([
            ['percentage' => 22, 'country' => 'US', 'region' => 42],
            ['percentage' => 10, 'country' => 'US', 'region' => 12], // Default store rate
            ]);

        $this->taxRules = $this->taxRuleFixtureFactory->createTaxRules([
            [
                'code' => 'Default Rule',
                'customer_tax_class_ids' => [$this->taxClasses['DefaultCustomerClass'], 3],
                'product_tax_class_ids' => [$this->taxClasses['DefaultProductClass']],
                'tax_rate_ids' => array_values($this->taxRates),
                'sort_order' => 0,
                'priority' => 0,
            ],
            [
                'code' => 'Higher Rate Rule',
                'customer_tax_class_ids' => [$this->taxClasses['DefaultCustomerClass'], 3],
                'product_tax_class_ids' => [$this->taxClasses['HigherProductClass']],
                'tax_rate_ids' => array_values($higherRates),
                'sort_order' => 0,
                'priority' => 0,
            ],
        ]);

        // For cleanup
        $this->taxRates = array_merge($this->taxRates, $higherRates);
    }

    /**
     * Helper function that tears down some default rules
     */
    private function tearDownDefaultRules()
    {
        $this->taxRuleFixtureFactory->deleteTaxRules(array_values($this->taxRules));
        $this->taxRuleFixtureFactory->deleteTaxRates(array_values($this->taxRates));
        $this->taxRuleFixtureFactory->deleteTaxClasses(array_values($this->taxClasses));
    }

}
