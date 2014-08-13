<?php
/**
 * BAT (L1 plan)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

class BatTests
{
    public static function suite()
    {
        $suite = new TestSuite('BAT');

        // Product
        $suite->addTestSuite('Magento\Bundle\Test\TestCase\BundleFixedTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateTest');
        $suite->addTestSuite('Magento\ConfigurableProduct\Test\TestCase\CreateConfigurableTest');
        $suite->addTestSuite('Magento\ConfigurableProduct\Test\TestCase\CreateWithAttributeTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateSimpleWithCustomOptionsAndCategoryTest');

        // Category
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Category\CreateTest');

        // Search
        $suite->addTestSuite('Magento\CatalogSearch\Test\TestCase\SearchTest');

        // Checkout
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\CheckMoneyOrderTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\ProductAdvancedPricingTest');

        // Sales rule
        $suite->addTestSuite('Magento\SalesRule\Test\TestCase\BasicPromoTest');

        // Stores
        $suite->addTestSuite('Magento\Store\Test\TestCase\StoreTest');

        return $suite;
    }
}
