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

        $t = new \Magento\Checkout\Test\TestCase\OnepageTest();
        $suite->addTestSuite('Magento\Bundle\Test\TestCase\BundleTest');
//        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Product\CreateConfigurableTest');
        $suite->addTestSuite('Magento\Catalog\Test\TestCase\Category\CreateTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\OnepageTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalExpress\CheckoutTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalExpress\CheckoutOnepageTest');
        return $suite;
    }
}
