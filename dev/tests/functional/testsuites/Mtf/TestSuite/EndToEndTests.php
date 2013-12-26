<?php
/**
 * End-to-end scenarios without 3-rd party solutions (L3 plan)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

class EndToEndTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('End-to-end Scenarios without 3-rd Party Solutions');

        // Products
        // Simple
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateProductTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\EditSimpleProductTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateSimpleWithCategoryTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\UnassignCategoryTest');
        // Grouped
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateGroupedTest');
        // Virtual
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateVirtualTest');
        // Configurable
        // Downloadable
        $suite->addTestSuite('Magento\Downloadable\Test\TestCase\Create\LinksPurchasedSeparatelyTest');
        // Gift Card
        $suite->addTestSuite('Magento\GiftCard\Test\TestCase\RequiredFieldsTest');
        // Bundle
        $suite->addTestSuite('Magento\Bundle\Test\TestCase\BundleDynamicTest');
        $suite->addTestSuite('Magento\Bundle\Test\TestCase\EditBundleTest');
        // Product configuration
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\ApplyMapTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\OutOfStockTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\UpsellTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CrosssellTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\RelatedProductTest');

        // Product search
        $suite->addTestSuite('Magento\CatalogSearch\Test\TestCase\AdvancedSearchTest');

        // Url rewrites
        $suite->addTestSuite('Magento\Backend\Test\TestCase\Urlrewrite\ProductTest');
        $suite->addTestSuite('Magento\Backend\Test\TestCase\Urlrewrite\CategoryTest');

        // Admin user
        $suite->addTestSuite('Magento\Pci\Test\TestCase\LockedTest');
        $suite->addTestSuite('Magento\User\Test\TestCase\UserWithRestrictedRoleTest');

        // Admin logging
        $suite->addTestSuite('Magento\Logging\Test\TestCase\LogReportTest');

        // Customer
        $suite->addTestSuite('Magento\Customer\Test\TestCase\BackendCustomerCreateTest');
        $suite->addTestSuite('Magento\Customer\Test\TestCase\CreateOnFrontendTest');

        // Customer Segment
        $suite->addTestSuite('Magento\CustomerSegment\Test\TestCase\CreateTest');

        // Review
        $suite->addTestSuite('Magento\Review\Test\TestCase\ReviewTest');

        // Orders. Backend
        $suite->addTestSuite('Magento\Sales\Test\TestCase\CreateOrderTest');

        // Tax
        $suite->addTestSuite('Magento\Tax\Test\TestCase\TaxRuleTest');

        // Catalog Price Rule
        $suite->addTestSuite('Magento\CatalogRule\Test\TestCase\CatalogPriceRule\ApplyCatalogPriceRuleTest');
        $suite->addTestSuite('Magento\CatalogRule\Test\TestCase\CatalogPriceRule\ApplyCustomerGroupCatalogRuleTest');

        // Currency
        $suite->addTestSuite('Magento\Directory\Test\TestCase\CurrencyTest');

        return $suite;
    }
}
