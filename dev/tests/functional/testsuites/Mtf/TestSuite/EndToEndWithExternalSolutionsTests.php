<?php
/**
 * End-to-end scenarios that works with 3-rd party solutions
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

class EndToEndWithExternalSolutionsTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('End-to-end Scenarios with 3-rd Party Solutions');

        // Checkout
        // 3D Secure
        $suite->addTestSuite('Magento\Centinel\Test\TestCase\CentinelPaymentsValidCcTest');
        $suite->addTestSuite('Magento\Centinel\Test\TestCase\CentinelPaymentsInvalidCcTest');
        // Guest checkout
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalAdvancedTest');
        // Guest checkout. PayPal Express
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalExpress\CheckoutOnepageTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalExpress\PayflowProTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalExpress\ProductPageTest');
        // PayPal Express
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\PaypalExpress\CheckoutOnepageTest');
        // Onepage Checkout
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\OnepageTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\ProductAdvancedPricingTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\ShippingCarrierTest');
        // Multishipping
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\MultishippingTest');

        // RMA
        $suite->addTestSuite('Magento\Rma\Test\TestCase\RmaTest');

        // Orders. Backend
        $suite->addTestSuite('Magento\Sales\Test\TestCase\CloseOrderTest');

        // VAT
        $suite->addTestSuite('Magento\Tax\Test\TestCase\AutomaticTaxApplyingTest');
        $suite->addTestSuite('Magento\Customer\Test\TestCase\VatGroupAssignmentTest');

        return $suite;
    }
}
