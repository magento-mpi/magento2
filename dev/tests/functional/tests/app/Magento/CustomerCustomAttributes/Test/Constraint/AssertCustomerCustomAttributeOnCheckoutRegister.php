<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
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
     * @param FixtureFactory $fixtureFactory
     * @param CheckoutCart $checkoutCart
     * @param CheckoutOnepage $checkoutOnepage
     * @param CatalogProductView $pageCatalogProductView
     * @param CatalogCategoryView $catalogCategoryView
     * @param CustomerCustomAttribute $customerAttribute
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        FixtureFactory $fixtureFactory,
        CheckoutCart $checkoutCart,
        CheckoutOnepage $checkoutOnepage,
        CatalogProductView $pageCatalogProductView,
        CatalogCategoryView $catalogCategoryView,
        CustomerCustomAttribute $customerAttribute
    ) {
        // Precondition
        $productSimple = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => 'product_with_category']);
        $productSimple->persist();
        $categoryName = $productSimple->getCategoryIds()[0]['name'];
        $productName = $productSimple->getName();

        // Steps
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        $catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $pageCatalogProductView->getViewBlock()->clickAddToCartButton();
        $checkoutCart->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        $checkoutOnepage->getLoginBlock()->guestCheckout();
        $attributeCode = $customerAttribute->getAttributeCode();
        \PHPUnit_Framework_Assert::assertTrue(
            $checkoutOnepage->getBillingBlock()->isCustomerAttributeVisible($attributeCode),
            'Customer Custom Attribute with attribute code: \'' . $attributeCode . '\' '
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
