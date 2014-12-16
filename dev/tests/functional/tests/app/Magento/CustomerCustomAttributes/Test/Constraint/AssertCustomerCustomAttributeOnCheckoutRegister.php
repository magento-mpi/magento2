<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerCustomAttributeOnCheckoutRegister
 * Assert that created customer attribute is available during register customer on checkout
 */
class AssertCustomerCustomAttributeOnCheckoutRegister extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created customer attribute is available during register customer on checkout
     *
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CatalogProductSimple $productSimple
     * @param CheckoutCart $checkoutCart
     * @param CheckoutOnepage $checkoutOnepage
     * @param CatalogProductView $catalogProductViewPage
     * @param CustomerCustomAttribute $customerAttribute
     * @param Browser $browser
     * @param CustomerCustomAttribute $initialCustomerAttribute
     * @return void
     */
    public function processAssert(
        CustomerAccountLogout $customerAccountLogout,
        CatalogProductSimple $productSimple,
        CheckoutCart $checkoutCart,
        CheckoutOnepage $checkoutOnepage,
        CatalogProductView $catalogProductViewPage,
        CustomerCustomAttribute $customerAttribute,
        Browser $browser,
        CustomerCustomAttribute $initialCustomerAttribute = null
    ) {
        // Precondition
        $productSimple->persist();
        $customerAccountLogout->open();

        // Steps
        $customerAttribute = $initialCustomerAttribute === null ? $customerAttribute : $initialCustomerAttribute;
        $browser->open($_ENV['app_frontend_url'] . $productSimple->getUrlKey() . '.html');
        $catalogProductViewPage->getViewBlock()->clickAddToCartButton();
        $checkoutCart->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        $checkoutOnepage->getLoginBlock()->registerCustomer();
        $checkoutOnepage->getLoginBlock()->clickContinue();
        \PHPUnit_Framework_Assert::assertTrue(
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
        return 'Customer Attribute is present during register customer on checkout.';
    }
}
