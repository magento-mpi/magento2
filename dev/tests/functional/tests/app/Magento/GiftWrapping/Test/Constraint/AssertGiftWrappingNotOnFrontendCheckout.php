<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftWrappingNotOnFrontendCheckout
 * Assert that deleted Gift Wrapping can not be found during one page checkout on frontend
 */
class AssertGiftWrappingNotOnFrontendCheckout extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that deleted Gift Wrapping can not be found during one page checkout on frontend
     *
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param Browser $browser
     * @param CheckoutOnepage $checkoutOnepage
     * @param GiftWrapping|GiftWrapping[] $giftWrapping
     * @param AddressInjectable $billingAddress
     * @param CatalogProductSimple $product
     * @param CustomerInjectable $customer
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        Browser $browser,
        CheckoutOnepage $checkoutOnepage,
        $giftWrapping,
        AddressInjectable $billingAddress,
        CatalogProductSimple $product,
        CustomerInjectable $customer,
        CustomerAccountLogout $customerAccountLogout
    ) {
        // Preconditions
        $customer->persist();
        $product->persist();
        // Steps
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickAddToCartButton();
        $checkoutCart->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        $checkoutOnepage->getLoginBlock()->loginCustomer($customer);
        $checkoutOnepage->getBillingBlock()->fillBilling($billingAddress);
        $checkoutOnepage->getBillingBlock()->clickContinue();
        $giftWrappingsAvailable = $checkoutOnepage->getGiftOptionsBlock()->getGiftWrappingsAvailable();
        $matches = [];
        $giftWrappings = !is_array($giftWrapping) ? [$giftWrapping] : $giftWrapping;
        foreach ($giftWrappings as $giftWrapping) {
            if (in_array($giftWrapping->getDesign(), $giftWrappingsAvailable)) {
                $matches[] = $giftWrapping->getDesign();
            }
        }
        $customerAccountLogout->open();
        \PHPUnit_Framework_Assert::assertEmpty(
            $matches,
            'Gift Wrapping is present in one page checkout on frontend.'
            . "\nLog:\n" . implode(";\n", $matches)
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift Wrapping can not be found during one page checkout on frontend.';
    }
}
