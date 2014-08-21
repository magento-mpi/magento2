<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Mtf\Client\Browser;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;

/**
 * Class AssertCustomerCustomAttributeNotOnCheckoutRegister
 * Assert that deleted customer attribute is not available during register customer on checkout
 */
class AssertCustomerCustomAttributeNotOnCheckoutRegister extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that deleted customer attribute is not available during register customer on checkout
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogProductSimple $productSimple
     * @param CheckoutCart $checkoutCart
     * @param CheckoutOnepage $checkoutOnepage
     * @param CatalogProductView $catalogProductViewPage
     * @param CustomerCustomAttribute $customerAttribute
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogProductSimple $productSimple,
        CheckoutCart $checkoutCart,
        CheckoutOnepage $checkoutOnepage,
        CatalogProductView $catalogProductViewPage,
        CustomerCustomAttribute $customerAttribute,
        Browser $browser
    ) {
        // Precondition
        $productSimple->persist();

        $cmsIndex->open();
        if ($cmsIndex->getLinksBlock()->isLinkVisible("Log Out")) {
            $cmsIndex->getLinksBlock()->openLink("Log Out");
        }

        // Steps
        $browser->open($_ENV['app_frontend_url'] . $productSimple->getUrlKey() . '.html');
        $catalogProductViewPage->getViewBlock()->clickAddToCartButton();
        $checkoutCart->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        $checkoutOnepage->getLoginBlock()->registerCustomer();
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
