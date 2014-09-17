<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringPayment\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

class RecurringPaymentTest extends Functional
{
    /**
     * Displaying payment schedule in shopping cart for products with recurring payment
     *
     * @ZephyrId MAGETWO-21881
     */
    public function testRecurringOptionsInCart()
    {
        $billingCycle = '12';
        $product = Factory::getFixtureFactory()->getMagentoRecurringPaymentSimpleProductWithRecurringPayment(
            array('recurring_billing_cycle' => $billingCycle, 'recurring_period' => 'month')
        );
        $product->persist();

        $productPage = Factory::getPageFactory()->getCatalogProductView();
        Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $productPage->getViewBlock()->addToCart($product);
        $checkoutCart = Factory::getPageFactory()->getCheckoutCartIndex();

        $expectedProductOptions = "Start Date\n"
            . "%s\nBilling Period\n"
            . "$billingCycle Month cycle.\n"
            . "Repeats until suspended or canceled.";
        $actualProductOptions = $checkoutCart->getCartBlock()->getCartItem($product)->getOptions();
        $this->assertStringMatchesFormat($expectedProductOptions, $actualProductOptions);
    }
}
