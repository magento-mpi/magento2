<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Abstract Class AbstractAssertMultipleWishlistExist
 * Assert that Wishlist exist or doesn't exist on 'My Account' page
 */
abstract class AbstractAssertMultipleWishlistExist extends AbstractConstraint
{
    /**
     * Assert that Wishlist exists on 'My Account' page
     *
     * @param CmsIndex $cmsIndex
     * @param WishlistIndex $wishlistIndex
     * @param MultipleWishlist $multipleWishlist
     * @param CustomerAccountIndex $customerAccountIndex
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        WishlistIndex $wishlistIndex,
        MultipleWishlist $multipleWishlist,
        CustomerAccountIndex $customerAccountIndex
    ) {
        $cmsIndex->open()->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        $isPresent = $wishlistIndex->getManagementBlock()->isWishlistVisible($multipleWishlist->getName());
        $this->assert($isPresent);
    }

    /**
     * Assert wish list is exist or doesn't exist
     *
     * @param bool $isPresent
     * @return void
     */
    abstract protected function assert($isPresent);
}
