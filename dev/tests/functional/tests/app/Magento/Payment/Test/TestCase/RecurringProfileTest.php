<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

class RecurringProfileTest extends Functional
{
    /**
     * Verify that a product with recurring options, added to the cart, has those options displayed in the cart
     */
    public function testRecurringOptionsInCart()
    {
        $billingCycle = '12';
        $product = Factory::getFixtureFactory()->getMagentoPaymentSimpleProductWithRecurringProfile(
            array('recurring_billing_cycle' => $billingCycle, 'recurring_period' => 'month')
        );
        $product->persist();

        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($product);
        $productPage->open();
        $productPage->getViewBlock()->addToCart($product);
        $checkoutCart = Factory::getPageFactory()->getCheckoutCart();

        $expectedProductOptions = "Start Date\n"
            . "%s\nBilling Period\n"
            . "$billingCycle Month cycle.\n"
            . "Repeats until suspended or canceled.";
        $actualProductOptions = $checkoutCart->getCartBlock()->getCartItemOptions($product);
        $this->assertStringMatchesFormat($expectedProductOptions, $actualProductOptions);
    }
}
