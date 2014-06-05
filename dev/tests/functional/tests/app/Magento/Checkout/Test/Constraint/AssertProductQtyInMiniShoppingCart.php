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
     * Assert that quantity in the mini shopping cart is equals to expected quantity from data set
     *
     * @param CmsIndex $cmsIndex
     * @param Cart $cart
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        Cart $cart
    ) {
        $productQtyInMiniCart = $cmsIndex->open()->getMiniCartBlock()->getProductQty();
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
