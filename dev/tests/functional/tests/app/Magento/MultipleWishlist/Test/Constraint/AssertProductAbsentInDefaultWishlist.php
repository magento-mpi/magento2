<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;

/**
 * Class AssertProductAbsentInDefaultWishlist
 * Assert that product is absent in default wishlist after moving it to another custom wishlist
 */
class AssertProductAbsentInDefaultWishlist extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product and quantity is present in default wishlist
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param WishlistIndex $wishlistIndex
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        WishlistIndex $wishlistIndex,
        InjectableFixture $product
    ) {
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        \PHPUnit_Framework_Assert::assertFalse(
            $wishlistIndex->getMultipleItemsBlock()->isProductVisible($product),
            "Product is present in default wishlist"
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product with correct quantity is absent in default wishlist';
    }
}
