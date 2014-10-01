<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductPresentOnMiniShoppingCart
 * Check that product is present on mini shopping cart
 */
class AssertProductPresentOnMiniShoppingCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert product is present on mini shopping cart
     *
     * @param CmsIndex $cmsIndex
     * @param array $products
     * @param int $deletedProductIndex
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, array $products, $deletedProductIndex)
    {
        $cmsIndex->open();
        $cmsIndex->getCartSidebarBlock()->waitCounterQty();
        unset($products[$deletedProductIndex]);
        foreach ($products as $product) {
            $productName = $product->getName();
            $cmsIndex->getCartSidebarBlock()->openMiniCart();
            \PHPUnit_Framework_Assert::assertTrue(
                $cmsIndex->getCartSidebarBlock()->getCartItem($product)->checkProductInMiniCart($product),
                'Product' . $productName . ' is absent on Mini Shopping Cart'
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Products is presents on Mini Shopping Cart.';
    }
}
