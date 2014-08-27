<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Fixture\FixtureFactory;
use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\Customer\Test\Fixture\AddressInjectable;
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
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param Browser $browser
     * @param CheckoutOnepage $checkoutOnepage
     * @param GiftWrapping $initialGiftWrapping
     * @param AddressInjectable $billingAddress
     * @return void
     */
    public function processAssert(
        FixtureFactory $fixtureFactory,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        Browser $browser,
        CheckoutOnepage $checkoutOnepage,
        GiftWrapping $initialGiftWrapping,
        AddressInjectable $billingAddress
    ) {
        // Preconditions
        $customer = $fixtureFactory->createByCode('customerInjectable');
        $customer->persist();
        $product = $fixtureFactory->createByCode('catalogProductSimple');
        $product->persist();
        // Steps
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickAddToCartButton();
        $checkoutCart->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        $checkoutOnepage->getLoginBlock()->loginCustomer($customer);
        $checkoutOnepage->getBillingBlock()->fillBilling($billingAddress);
        $checkoutOnepage->getBillingBlock()->clickContinue();
        \PHPUnit_Framework_Assert::assertFalse(
            $checkoutOnepage->getShippingMethodBlock()->isGiftWrappingAvailable($initialGiftWrapping->getDesign()),
            'Gift Wrapping \'' . $initialGiftWrapping->getDesign() . '\' is present in one page checkout on frontend.'
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
