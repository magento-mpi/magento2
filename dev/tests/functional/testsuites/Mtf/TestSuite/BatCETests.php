<?php
/**
 * BAT CE
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

class BatCETests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('BAT CE');

        // Product
        $suite->addTestSuite('Magento\Bundle\Test\TestCase\BundleFixedTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateConfigurableTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\Configurable\CreateWithAttributeTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateSimpleWithCustomOptionsAndCategoryTest');

        // Category
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Category\CreateTest');

        // Search
        $suite->addTestSuite('Magento\CatalogSearch\Test\TestCase\SearchTest');

        // Checkout
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\CheckMoneyOrderTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\ProductAdvancedPricingTest');

        // Stores
        $suite->addTestSuite('Magento\Store\Test\TestCase\StoreTest');

        return $suite;
    }
}
