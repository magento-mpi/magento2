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
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertGiftWrappingOnFrontendCheckout
 * Assert that Gift Wrapping can be found during one page checkout on frontend
 */
class AssertGiftWrappingOnFrontendCheckout extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Gift Wrapping can be found during one page checkout on frontend
     *
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param Browser $browser
     * @param CheckoutOnepage $checkoutOnepage
     * @param array $giftWrapping
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
        array $giftWrapping,
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
        foreach ($giftWrapping as $item) {
            if (in_array($item->getDesign(), $giftWrappingsAvailable)) {
                $matches[] = $item->getDesign();
            }
        }
        $customerAccountLogout->open();
        \PHPUnit_Framework_Assert::assertNotEmpty(
            $matches,
            'Gift Wrapping is not present in one page checkout on frontend.'
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
        return 'Gift Wrapping can be found during one page checkout on frontend.';
    }
}
