<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Magento\Customer\Test\Page\CustomerAccountLogout;
use Mtf\Client\Browser;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;

/**
 * Class AssertCustomerCustomAttributeOnCheckoutRegister
 * Assert that created customer attribute is available during register customer on checkout
 */
class AssertCustomerCustomAttributeOnCheckoutRegister extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created customer attribute is available during register customer on checkout
     *
     * @param CmsIndex $cmsIndex
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
