<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\GroupedProduct\Test\Fixture\GroupedProductInjectable;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGroupedProductInCustomWishlist
 * Assert that grouped product is present in custom wishlist
 */
class AssertGroupedProductInCustomWishlist extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that grouped product is present in custom wishlist
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param MultipleWishlist $multipleWishlist
     * @param WishlistIndex $wishlistIndex
     * @param GroupedProductInjectable $product
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        MultipleWishlist $multipleWishlist,
        WishlistIndex $wishlistIndex,
        GroupedProductInjectable $product
    ) {
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        $wishlistIndex->getManagementBlock()->selectedWishlistByName($multipleWishlist->getName());

        \PHPUnit_Framework_Assert::assertTrue(
            $wishlistIndex->getMultipleItemsBlock()->getItemProduct($product)->isVisible(),
            $product->getName() . 'is absent in custom wishlist.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Grouped product is present in custom wishlist';
    }
}
