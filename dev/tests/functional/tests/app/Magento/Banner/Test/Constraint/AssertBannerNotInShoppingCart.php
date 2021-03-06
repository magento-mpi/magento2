<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertBannerInShoppingCart
 * Check that banner is absent on Shopping Cart page
 */
class AssertBannerNotInShoppingCart extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that banner is absent on Shopping Cart page
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductView $pageCatalogProductView
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CheckoutCart $pageCheckoutCart
     * @param BannerInjectable $banner
     * @param CustomerInjectable $customer
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CatalogProductView $pageCatalogProductView,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CheckoutCart $pageCheckoutCart,
        BannerInjectable $banner,
        CustomerInjectable $customer = null
    ) {
        if ($customer !== null) {
            $this->objectManager->create(
                'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
                ['customer' => $customer]
            )->run();
        } else {
            $cmsIndex->open();
        }
        $productName = $product->getName();
        $cmsIndex->getTopmenu()->selectCategoryByName($product->getCategoryIds()[0]);
        $catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $pageCatalogProductView->getViewBlock()->clickAddToCartButton();
        \PHPUnit_Framework_Assert::assertFalse(
            $pageCheckoutCart->getBannerCartBlock()->checkWidgetBanners($banner, $customer),
            'Banner is presents on Shopping Cart'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Banner is absent on Shopping Cart.";
    }
}
