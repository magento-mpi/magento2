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
     * @param CmsIndex $cmsIndex
     * @param array $products
     * @param int $deletedProductIndex
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, array $products, $deletedProductIndex)
    {
        $cmsIndex->open();
        $cmsIndex->getCartSidebarBlock()->waitCounterQty();
        $deletedProductName = $products[$deletedProductIndex]->getName();
        $cmsIndex->getCartSidebarBlock()->openMiniCart();
        \PHPUnit_Framework_Assert::assertFalse(
            $cmsIndex->getCartSidebarBlock()->getCartItem($products[$deletedProductIndex])
                ->checkProductInMiniCart($products[$deletedProductIndex]),
            'Product' . $deletedProductName . ' is presents on Mini Shopping Cart'
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
