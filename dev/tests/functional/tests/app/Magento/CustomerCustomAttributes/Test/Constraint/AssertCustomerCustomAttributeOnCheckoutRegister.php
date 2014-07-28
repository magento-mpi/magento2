<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
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
     * @param CatalogProductSimple $productSimple
     * @param CheckoutCart $checkoutCart
     * @param CheckoutOnepage $checkoutOnepage
     * @param CatalogProductView $catalogProductViewPage
     * @param CustomerCustomAttribute $customerAttribute
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $productSimple,
        CheckoutCart $checkoutCart,
        CheckoutOnepage $checkoutOnepage,
        CatalogProductView $catalogProductViewPage,
        CustomerCustomAttribute $customerAttribute
    ) {
        // Precondition
        $productSimple->persist();

        // Steps
        $catalogProductViewPage->init($productSimple);
        $catalogProductViewPage->open();
        $catalogProductViewPage->getViewBlock()->clickAddToCartButton();
        $checkoutCart->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        $checkoutOnepage->getLoginBlock()->guestCheckout();
        \PHPUnit_Framework_Assert::assertTrue(
            $checkoutOnepage->getBillingBlock()->isCustomerAttributeVisible($customerAttribute),
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
