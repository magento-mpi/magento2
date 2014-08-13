<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\MultipleWishlist\Test\Page\MultipleWishlistIndex;

/**
 * Class AssertMultipleWishlistPresentInMyAccount
 * Assert that Wishlist exists on 'My Account' page
 */
class AssertMultipleWishlistPresentInMyAccount extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that Wishlist exists on 'My Account' page
     *
     * @param CmsIndex $cmsIndex
     * @param MultipleWishlistIndex $multipleWishlistIndex
     * @param MultipleWishlist $multipleWishlist
     * @param CustomerAccountIndex $customerAccountIndex
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        MultipleWishlistIndex $multipleWishlistIndex,
        MultipleWishlist $multipleWishlist,
        CustomerAccountIndex $customerAccountIndex
    ) {
        $cmsIndex->open()->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        $isPresent = $multipleWishlistIndex->getManagementBlock()->isWishlistVisible($multipleWishlist->getName());
        \PHPUnit_Framework_Assert::assertTrue($isPresent, 'Multiple wish list is not exist on "My Account" page.');
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Multiple wish list exists on "My Account" page.';
    }
}
