<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

class BatTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('Tests');

        $suite->addTestSuite('Magento\Bundle\Test\TestCase\BundleTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateConfigurableTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Category\CreateTest');
        $suite->addTestSuite('Magento\Sales\Test\TestCase\CancelOrderTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalExpress\ProductPageTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalExpress\CheckoutOnepageTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalExpress\PayflowProTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\OnepageTest');

        return $suite;
    }
}
