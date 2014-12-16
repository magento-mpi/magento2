<?php
/**
 * End-to-end scenarios that works with 3-rd party solutions
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalCreditCardTest');
        // Guest checkout. PayPal Express
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalExpress\CheckoutOnepageTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalExpress\PayflowProTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalExpress\ProductPageTest');
        // Guest checkout.  PayPal Standard
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\Guest\PaypalStandardTest');
        // PayPal Express
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\PaypalExpress\CheckoutOnepageTest');
        // Onepage Checkout
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\OnepageTest');
        $suite->addTestSuite('Magento\Checkout\Test\TestCase\ShippingCarrierTest');
        // Multishipping
        $suite->addTestSuite('Magento\Multishipping\Test\TestCase\MultishippingTest');

        // RMA
        $suite->addTestSuite('Magento\Rma\Test\TestCase\RmaTest');

        // Orders. Backend
        $suite->addTestSuite('Magento\Sales\Test\TestCase\CloseOrderTest');

        // Refunds
        $suite->addTestSuite('Magento\Sales\Test\TestCase\OnlineRefundTest');
        $suite->addTestSuite('Magento\Sales\Test\TestCase\OfflineRefundTest');

        // VAT
        $suite->addTestSuite('Magento\Tax\Test\TestCase\AutomaticTaxApplyingTest');
        $suite->addTestSuite('Magento\Customer\Test\TestCase\VatGroupAssignmentTest');

        return $suite;
    }
}
