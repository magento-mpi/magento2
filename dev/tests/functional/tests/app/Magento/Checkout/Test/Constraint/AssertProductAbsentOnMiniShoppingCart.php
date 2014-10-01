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
use Magento\Checkout\Test\Fixture\Cart;

/**
 * Class AssertProductAbsentOnMiniShoppingCart
 * Check that product is absent on mini shopping cart
 */
class AssertProductAbsentOnMiniShoppingCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert product is absent on mini shopping cart
     *
     * @param Cart $cart
     * @param CmsIndex $cmsIndex
     * @param array $products
     * @return void
     */
    public function processAssert(Cart $cart, CmsIndex $cmsIndex, $products)
    {
        $cmsIndex->open();
        $cmsIndex->getCartSidebarBlock()->waitCounterQty();
        $deletedProductName = $cart->getItems()[1]->getData()['product_name'];
        $cmsIndex->getCartSidebarBlock()->openMiniCart();
        \PHPUnit_Framework_Assert::assertFalse(
            $cmsIndex->getCartSidebarBlock()->getCartItem($products[0])->checkProductInMiniCart($deletedProductName),
            'Product is presents on Mini Shopping Cart'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is absent on Mini Shopping Cart.';
    }
}
