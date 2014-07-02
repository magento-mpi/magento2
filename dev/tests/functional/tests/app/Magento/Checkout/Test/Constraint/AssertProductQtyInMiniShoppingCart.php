<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Checkout\Test\Fixture\Cart;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductQtyInMiniShoppingCart
 */
class AssertProductQtyInMiniShoppingCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product quantity in the mini shopping cart is equals to expected quantity from data set
     *
     * @param CmsIndex $cmsIndex
     * @param Cart $cart
     * @param CatalogProductSimple $product
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        Cart $cart,
        CatalogProductSimple $product
    ) {
        $productQtyInMiniCart = $cmsIndex->open()->getCartSidebarBlock()->getProductQty($product->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $productQtyInMiniCart,
            $cart->getQty(),
            'Mini shopping cart product qty: \'' . $productQtyInMiniCart
            . '\' not equals with qty from data set: \'' . $cart->getQty() . '\''
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Quantity in the mini shopping cart equals to expected quantity from data set.';
    }
}
