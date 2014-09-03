<?php
/**
 * End-to-end scenarios without 3-rd party solutions for CE
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

class EndToEndCETests
{
    public static function suite()
    {
        $suite = new TestSuite('End-to-end Scenarios without 3-rd Party Solutions for CE');

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
        $suite->addTestSuite('Magento\ConfigurableProduct\Test\TestCase\EditConfigurableTest');
        // Downloadable
        $suite->addTestSuite('Magento\Downloadable\Test\TestCase\Create\LinksPurchasedSeparatelyTest');
        // Bundle
        $suite->addTestSuite('Magento\Bundle\Test\TestCase\BundleDynamicTest');
        $suite->addTestSuite('Magento\Bundle\Test\TestCase\EditBundleTest');
        // Product configuration
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\ApplyMapTest');
        $suite->addTestSuite('Magento\ConfigurableProduct\Test\TestCase\ApplyMapConfigurableTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\OutOfStockTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\UpsellTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CrosssellTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\RelatedProductTest');

        // Product search
        $suite->addTestSuite('Magento\CatalogSearch\Test\TestCase\AdvancedSearchTest');

        // Url rewrites
        $suite->addTestSuite('Magento\Urlrewrite\Test\TestCase\ProductTest');
        $suite->addTestSuite('Magento\Urlrewrite\Test\TestCase\CategoryTest');

        // Admin user
        $suite->addTest(new \Magento\User\Test\TestCase\UserWithRestrictedRoleTest('testAclRoleWithFullGwsScope'));

        // Customer
        $suite->addTestSuite('Magento\Customer\Test\TestCase\BackendCustomerCreateTest');
        $suite->addTestSuite('Magento\Customer\Test\TestCase\CreateOnFrontendTest');

        // Review
        $suite->addTestSuite('Magento\Review\Test\TestCase\ReviewTest');

        // Orders. Backend
        $suite->addTestSuite('Magento\Sales\Test\TestCase\CreateOrderTest');

        // Tax
        $suite->addTestSuite('Magento\Tax\Test\TestCase\TaxRuleTest');

        // Catalog Price Rule
        $suite->addTestSuite('Magento\CatalogRule\Test\TestCase\CatalogPriceRule\ApplyCustomerGroupCatalogRuleTest');

        // Currency
        $suite->addTestSuite('Magento\Directory\Test\TestCase\CurrencyTest');

        // Layered navigation
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Layer\FilterProductListTest');

        // Assign products to a category
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Category\AssignProductTest');

        return $suite;
    }
}
