<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerCustomAttributeNotOnCheckoutRegister
 * Assert that deleted customer attribute is not available during register customer on checkout
 */
class AssertCustomerCustomAttributeNotOnCheckoutRegister extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that deleted customer attribute is not available during register customer on checkout
     *
     * @param CatalogProductSimple $productSimple
     * @param CheckoutCart $checkoutCart
     * @param CheckoutOnepage $checkoutOnepage
     * @param CatalogProductView $catalogProductViewPage
     * @param CustomerCustomAttribute $customerAttribute
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $productSimple,
        CheckoutCart $checkoutCart,
        CheckoutOnepage $checkoutOnepage,
        CatalogProductView $catalogProductViewPage,
        CustomerCustomAttribute $customerAttribute,
        Browser $browser
    ) {
        // Precondition
        $productSimple->persist();

        // Steps
        $browser->open($_ENV['app_frontend_url'] . $productSimple->getUrlKey() . '.html');
        $catalogProductViewPage->getViewBlock()->clickAddToCartButton();
        $checkoutCart->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        $checkoutOnepage->getLoginBlock()->registerCustomer();
        $checkoutOnepage->getLoginBlock()->clickContinue();
        \PHPUnit_Framework_Assert::assertFalse(
            $checkoutOnepage->getCustomerAttributeBillingBlock()->isCustomerAttributeVisible($customerAttribute),
            'Customer Custom Attribute with attribute code: \'' . $customerAttribute->getAttributeCode() . '\' '
            . 'is absent during register customer on checkout.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Attribute is not available during register customer on checkout.';
    }
}
