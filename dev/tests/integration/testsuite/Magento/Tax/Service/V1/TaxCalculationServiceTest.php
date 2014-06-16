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
     * @dataProvider calculateTaxNoTaxInclDataProvider
     * @magentoConfigFixture current_store tax/calculation/algorithm TOTAL_BASE_CALCULATION
     */
    public function testCalculateTaxTotalBasedNoTaxIncl($quoteDetailsData, $expectedTaxDetails, $storeId = null)
    {
        $quoteDetailsData = $this->performTaxClassSubstitution($quoteDetailsData);

        $quoteDetails = $this->quoteDetailsBuilder->populateWithArray($quoteDetailsData)->create();

        $taxDetails = $this->taxCalculationService->calculateTax($quoteDetails, $storeId);

        $this->assertEquals($expectedTaxDetails, $taxDetails->__toArray());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     * @dataProvider calculateTaxTaxInclDataProvider
     * @magentoConfigFixture current_store tax/calculation/algorithm TOTAL_BASE_CALCULATION
     */
    public function testCalculateTaxTotalBasedTaxIncl($quoteDetailsData, $expectedTaxDetails, $storeId = null)
    {
        $quoteDetailsData = $this->performTaxClassSubstitution($quoteDetailsData);

        $quoteDetails = $this->quoteDetailsBuilder->populateWithArray($quoteDetailsData)->create();

        $taxDetails = $this->taxCalculationService->calculateTax($quoteDetails, $storeId);

        $this->assertEquals($expectedTaxDetails, $taxDetails->__toArray());
    }


    public function calculateTaxNoTaxInclDataProvider()
    {
        $prodNoTaxInclBase = [
            'quote_details' => [
                'shipping_address' => [
                    'postcode' => '55555',
                    'country_id' => 'US',
                    'region' => ['region_id' => 42],
                ],
                'items' => [
                    [
                        'code' => 'code',
                        'type' => 'type',
                        'quantity' => 1,
                        'unit_price' => 10.0,
                        'row_total' => 10.0,
                        'tax_included' => false,
                    ],
                ],
                'customer_tax_class_id' => 'DefaultCustomerClass'
            ],
            'expected_tax_details' => [
                'subtotal' => 10.0,
                'tax_amount' => 0.0,
                'discount_amount' => 0.0,
                'items' => [],
            ],
            'store_id' => null,
        ];

        $prodQuoteDetailItemBase = [
            'code' => 'code',
            'type' => 'type',
            'quantity' => 1,
            'unit_price' => 10.0,
            'row_total' => 10.0,
            'tax_included' => false,
        ];

        $quoteDetailItemWithDefaultProductTaxClass = $prodQuoteDetailItemBase;
        $quoteDetailItemWithDefaultProductTaxClass['tax_class_id'] = 'DefaultProductClass';

        $prodExpectedItemWithNoProductTaxClass = [
            'tax_amount' => 0,
            'price' => 10.0,
            'price_incl_tax' => 10.0,
            'row_total' => 10.0,
            'row_total_incl_tax' => 10.0,
            'taxable_amount' => 10.0,
            'code' => 'code',
            'type' => 'type',
            'tax_percent' => 0,
        ];

        $prodExpectedItemWithDefaultProductTaxClass = [
            'tax_amount' => 0.75,
            'price' => 10.0,
            'price_incl_tax' => 10.75,
            'row_total' => 10.0,
            'row_total_incl_tax' => 10.75,
            'taxable_amount' => 10.0,
            'code' => 'code',
            'type' => 'type',
            'tax_percent' => 7.5,
        ];

        $prodWithStoreIdWithTaxClassId = $prodNoTaxInclBase;
        $prodWithStoreIdWithoutTaxClassId = $prodNoTaxInclBase;
        $prodWithoutStoreIdWithTaxClassId = $prodNoTaxInclBase;
        $prodWithoutStoreIdWithoutTaxClassId = $prodNoTaxInclBase;

        $prodWithStoreIdWithTaxClassId['store_id'] = 1;
        $prodWithStoreIdWithTaxClassId['quote_details']['items'][] = $quoteDetailItemWithDefaultProductTaxClass;
        $prodWithStoreIdWithTaxClassId['expected_tax_details']['tax_amount'] = 0.75;
        $prodWithStoreIdWithTaxClassId['expected_tax_details']['items'][] =
            $prodExpectedItemWithDefaultProductTaxClass;

        $prodWithStoreIdWithoutTaxClassId['store_id'] = 1;
        $prodWithStoreIdWithoutTaxClassId['quote_details']['items'][] = $prodQuoteDetailItemBase;
        $prodWithStoreIdWithoutTaxClassId['expected_tax_details']['items'][] =
            $prodExpectedItemWithNoProductTaxClass;

        $prodWithoutStoreIdWithTaxClassId['quote_details']['items'][] =
            $quoteDetailItemWithDefaultProductTaxClass;
        $prodWithoutStoreIdWithTaxClassId['expected_tax_details']['tax_amount'] = 0.75;
        $prodWithoutStoreIdWithTaxClassId['expected_tax_details']['items'][] =
            $prodExpectedItemWithDefaultProductTaxClass;

        $prodWithoutStoreIdWithoutTaxClassId['quote_details']['items'][] = $prodQuoteDetailItemBase;
        $prodWithoutStoreIdWithoutTaxClassId['expected_tax_details']['items'][] =
            $prodExpectedItemWithNoProductTaxClass;

        return [
            'product with store id, with tax class id' => $prodWithStoreIdWithTaxClassId,
            'product with store id, without tax class id' => $prodWithStoreIdWithoutTaxClassId,
            'product without store id, with tax class id' => $prodWithoutStoreIdWithTaxClassId,
            'product without store id, without tax class id' => $prodWithoutStoreIdWithoutTaxClassId,
        ];
    }

    public function calculateTaxTaxInclDataProvider()
    {
        $productTaxInclBase = [
            'quote_details' => [
                'shipping_address' => [
                    'postcode' => '55555',
                    'country_id' => 'US',
                    'region' => ['region_id' => 42],
                ],
                'items' => [
                    [
                        'code' => 'code',
                        'type' => 'type',
                        'quantity' => 1,
                        'unit_price' => 10.0,
                        'row_total' => 10.0,
                        'tax_included' => true,
                    ],
                ],
                'customer_tax_class_id' => 'DefaultCustomerClass'
            ],
            'expected_tax_details' => [
                'subtotal' => 10.0,
                'tax_amount' => 0.0,
                'discount_amount' => 0.0,
                'items' => [],
            ],
            'store_id' => null,
        ];

        $productTaxInclQuoteDetailItemBase = [
            'code' => 'code',
            'type' => 'type',
            'quantity' => 1,
            'unit_price' => 10.0,
            'row_total' => 10.0,
            'tax_included' => true,
        ];

        $quoteDetailTaxInclItemWithDefaultProductTaxClass = $productTaxInclQuoteDetailItemBase;
        $quoteDetailTaxInclItemWithDefaultProductTaxClass['tax_class_id'] = 'DefaultProductClass';

        $productTaxInclExpectedItemWithNoProductTaxClass = [
            'tax_amount' => 0,
            'price' => 10.0,
            'price_incl_tax' => 10.0,
            'row_total' => 10.0,
            'row_total_incl_tax' => 10.0,
            'taxable_amount' => 10.0,
            'code' => 'code',
            'type' => 'type',
            'tax_percent' => 0,
        ];

        $productTaxInclExpectedItemWithDefaultProductTaxClass = [
            'tax_amount' => 0.70,
            'price' => 9.30,
            'price_incl_tax' => 10.00,
            'row_total' => 9.30,
            'row_total_incl_tax' => 10.0,
            'taxable_amount' => 9.30,
            'code' => 'code',
            'type' => 'type',
            'tax_percent' => 7.5,
        ];

        $productInclTaxWithStoreIdWithTaxClassId = $productTaxInclBase;
        $productInclTaxWithStoreIdWithoutTaxClassId = $productTaxInclBase;
        $productInclTaxWithoutStoreIdWithTaxClassId = $productTaxInclBase;
        $productInclTaxWithoutStoreIdWithoutTaxClassId = $productTaxInclBase;

        $productInclTaxWithStoreIdWithTaxClassId['store_id'] = 1;
        $productInclTaxWithStoreIdWithTaxClassId['quote_details']['items'][] =
            $quoteDetailTaxInclItemWithDefaultProductTaxClass;
        $productInclTaxWithStoreIdWithTaxClassId['expected_tax_details']['tax_amount'] = 0.70;
        $productInclTaxWithStoreIdWithTaxClassId['expected_tax_details']['subtotal'] = 9.30;
        $productInclTaxWithStoreIdWithTaxClassId['expected_tax_details']['items'][] =
            $productTaxInclExpectedItemWithDefaultProductTaxClass;
        $productInclTaxWithStoreIdWithTaxClassId['expected_tax_details']['items'][0]['taxable_amount'] = 10.00;

        $productInclTaxWithStoreIdWithoutTaxClassId['store_id'] = 1;
        $productInclTaxWithStoreIdWithoutTaxClassId['quote_details']['items'][] =
            $productTaxInclQuoteDetailItemBase;
        $productInclTaxWithStoreIdWithoutTaxClassId['expected_tax_details']['items'][] =
            $productTaxInclExpectedItemWithNoProductTaxClass;

        $productInclTaxWithoutStoreIdWithTaxClassId['quote_details']['items'][] =
            $quoteDetailTaxInclItemWithDefaultProductTaxClass;
        $productInclTaxWithoutStoreIdWithTaxClassId['expected_tax_details']['tax_amount'] = 0.70;
        $productInclTaxWithoutStoreIdWithTaxClassId['expected_tax_details']['subtotal'] = 9.30;
        $productInclTaxWithoutStoreIdWithTaxClassId['expected_tax_details']['items'][] =
            $productTaxInclExpectedItemWithDefaultProductTaxClass;
        /* TODO: BUG? */
        $productInclTaxWithoutStoreIdWithTaxClassId['expected_tax_details']['items'][0]['taxable_amount'] = 10.00;

        $productInclTaxWithoutStoreIdWithoutTaxClassId['quote_details']['items'][] = $productTaxInclQuoteDetailItemBase;
        $productInclTaxWithoutStoreIdWithoutTaxClassId['expected_tax_details']['items'][] =
            $productTaxInclExpectedItemWithNoProductTaxClass;

        return [
            'product incl tax with store id, with tax class id' => $productInclTaxWithStoreIdWithTaxClassId,
            'product incl tax with store id, without tax class id' => $productInclTaxWithStoreIdWithoutTaxClassId,
            'product incl tax without store id, with tax class id' => $productInclTaxWithoutStoreIdWithTaxClassId,
            'product incl tax without store id, without tax class id' => $productInclTaxWithoutStoreIdWithoutTaxClassId,
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

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
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
                    'price_incl_tax' => 11.83, // Unit price would have been 11.825 but row price is 11.83 (rounding)
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

        $twoProductsInclTax = $baseQuote;
        $twoProductsInclTax['items'] = [
            [
                'code' => 'sku_1',
                'type' => 'product',
                'quantity' => 10,
                'unit_price' => 1.075,
                'row_total' => 10.75,
                'tax_class_id' => 'DefaultProductClass',
                'tax_included' => true,
            ],
            [
                'code' => 'sku_2',
                'type' => 'product',
                'quantity' => 20,
                'unit_price' => 11.825,
                'row_total' => 236.5,
                'tax_class_id' => 'DefaultProductClass',
                'tax_included' => true,
            ]
        ];
        $twoProductInclTaxResults = $twoProductsResults;
        // TODO: I think this is a bug, but the old code behaved this way so keeping it for now.
        $twoProductInclTaxResults['items'][0]['taxable_amount'] = 10.75;
        $twoProductInclTaxResults['items'][1]['taxable_amount'] = 236.5;

        return [
            'one product' => [
                'quote_details' => $oneProduct,
                'expected_tax_details' => $oneProductResults,
            ],
            'one product, tax included' => [
                'quote_details' => $oneProductInclTax,
                'expected_tax_details' => $oneProductInclTaxResults,
            ],
            'one product, tax included but differs from store rate' => [
                'quote_details' => $oneProductInclTaxDiffRate,
                'expected_tax_details' => $oneProductInclTaxDiffRateResults,
            ],
            'two products' => [
                'quote_details' => $twoProducts,
                'expected_tax_details' => $twoProductsResults,
            ],
            'two products, tax included' => [
                'quote_details' => $twoProductsInclTax,
                'expected_tax_details' => $twoProductInclTaxResults,
            ],
        ];
    }

    /**
     * Substitutes an ID for the name of a tax class in a tax class ID field.
     *
     * @param array $data
     * @return array
     */
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
