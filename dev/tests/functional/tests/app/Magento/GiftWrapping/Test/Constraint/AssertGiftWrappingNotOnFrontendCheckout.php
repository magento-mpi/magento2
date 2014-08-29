<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertGiftWrappingNotOnFrontendCheckout
 * Assert that deleted Gift Wrapping can not be found during one page checkout on frontend
 */
class AssertGiftWrappingNotOnFrontendCheckout extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that deleted Gift Wrapping can not be found during one page checkout on frontend
     *
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param Browser $browser
     * @param CheckoutOnepage $checkoutOnepage
     * @param GiftWrapping $giftWrapping
     * @param AddressInjectable $billingAddress
     * @param CatalogProductSimple $product
     * @param CustomerInjectable $customer
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        Browser $browser,
        CheckoutOnepage $checkoutOnepage,
        GiftWrapping $giftWrapping,
        AddressInjectable $billingAddress,
        CatalogProductSimple $product,
        CustomerInjectable $customer
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
        \PHPUnit_Framework_Assert::assertFalse(
            $checkoutOnepage->getGiftOptionsBlock()->isGiftWrappingAvailable($giftWrapping),
            'Gift Wrapping \'' . $giftWrapping->getDesign() . '\' is present in one page checkout on frontend.'
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
